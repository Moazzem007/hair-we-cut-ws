<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\OpayoService;
use App\Models\PaymentOrders as Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    // 1) Create Order (API)
    public function createPaymentOrder(Request $r)
    {
        $data = $r->validate(['appointment_id'=>'required|numeric','amount'=>'required|numeric','reference'=>'nullable|string']);
        // create local order (simplified)
        $order = Order::create([
            'reference' => $data['reference'] ?? 'ORD-'.time(),
            'amount' => intval(round($data['amount'] * 100)),
            'currency' => 'GBP'
        ]);

        $appointment = Appointment::find($data['appointment_id']);
        $appointment->payment_status = 'pending';
        $appointment->update();

        return response()->json([
            'order_id' => $order->id,
            'checkout_url' => url('/checkout/'.$order->id.'/'.$data['appointment_id'])
        ], 201);
    }

    // 2) Checkout page (webview) — returns Blade with merchantSessionKey
    public function checkoutPage(Order $order, Appointment $appointment)
    {
        // create merchantSessionKey server-side
        $vendorName = config('services.opayo.vendor_name', 'sandbox');
        $resp = $this->opayo->createMerchantSessionKey($vendorName);
        if ($resp->failed()) {
            // \Log::error('MSK failed', ['status'=>$resp->status(),'body'=>$resp->body()]);
            abort(500, 'Payment provider error');
        }
        $body = $resp->json();
        $merchantSessionKey = $body['merchantSessionKey'] ?? null;
        return view('checkout', compact('order','merchantSessionKey', 'appointment'));
    }

    // 3) Register transaction: backend receives cardIdentifier from drop-in
public function registerTransaction(Request $r)
    {
        Log::info('Opayo: Called registerTransaction');

        // Validate incoming request
        $data = $r->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
            'order_id'       => 'required|integer|exists:orders,id',
            'merchantSessionKey' => 'required|string',
            'cardIdentifier'     => 'required|string'
        ]);

        $order = Order::findOrFail($data['order_id']);
        $appointment = Appointment::findOrFail($data['appointment_id']);
        $customer = Customer::findOrFail($appointment->customer_id);

        // Validate essential customer fields
        if (!$customer->postal_code || !$customer->billing_address) {
            return response()->json([
                'status' => 422,
                'body' => ['errors' => [[
                    'description' => !$customer->postal_code ? 'Postal code not found' : 'Billing address not found',
                    'code' => 1016
                ]]]
            ], 422);
        }

        // Validate order amount
        if (!is_numeric($order->amount) || $order->amount <= 0) {
            return response()->json([
                'status' => 422,
                'body' => ['errors' => [[
                    'description' => 'Invalid order amount',
                    'code' => 1016
                ]]]
            ], 422);
        }

        $vendorTxCode = 'order-' . $order->id . '-' . uniqid();
        $amountInPence = (int) round(floatval($order->amount) * 100);

        // Prepare payload for Opayo with strongCustomerAuthentication and notificationURL
        $payload = [
    "transactionType" => "Payment",
    "vendorTxCode"    => $vendorTxCode,
    "amount"          => $amountInPence,
    "currency"        => "GBP",
    "description"     => "Order #{$order->id} payment",
    "paymentMethod"   => [
        "card" => [
            "merchantSessionKey" => $data['merchantSessionKey'],
            "cardIdentifier"     => $data['cardIdentifier'],
            "reusable"           => false,
        ],
    ],
    "customerFirstName" => $customer->name ?? "Customer",
    "customerLastName"  => "Name",
    "customerEmail"     => $customer->email ?? "unknown@example.com",
    "customerPhone"     => $customer->contact ?? null,
    "billingAddress"    => [
        "address1"   => $customer->billing_address,
        "city"       => $customer->city ?? "N/A",
        "postalCode" => $customer->postal_code,
        "country"    => "GB",
    ],
    "apply3DSecure" => "Force",
    "strongCustomerAuthentication" => [
        "notificationURL" => url('/3ds-notification'), // Must be HTTPS
        "browserIP" => $r->ip() ?: '1.1.1.1',
        "browserAcceptHeader" => $r->header('Accept') ?? '*/*',
        "browserUserAgent" => $r->header('User-Agent') ?? 'Mozilla/5.0',
        "browserJavaEnabled" => true,
        "browserJavascriptEnabled" => true,
        "browserLanguage" => substr($r->header('Accept-Language', 'en-GB'), 0, 8),
        "browserColorDepth" => (string)($r->input('browserColorDepth') ?? 24),
        "browserScreenHeight" => (string)($r->input('browserScreenHeight') ?? '1080'),
        "browserScreenWidth" => (string)($r->input('browserScreenWidth') ?? '1920'),
        "browserTZ" => (string)($r->input('browserTZ') ?? '0'),
        "challengeWindowSize" => "Small",
        "transType" => "GoodsAndServicePurchase",
        "threeDSRequestorAuthenticationInfo" => [
            "threeDSReqAuthMethod" => "01",
            "threeDSReqAuthTimestamp" => now()->format('YmdHis'),
            "threeDSReqAuthData" => "fido"
        ],
        "threeDSRequestorPriorAuthenticationInfo" => [
            "threeDSReqPriorAuthMethod" => "FrictionlessAuthentication", // or "ChallengeAuthentication", "AVSVerified", "OtherIssuerMethods"
            "threeDSReqPriorAuthTimestamp" => now()->subHours(1)->format('YmdHi'), // Format: YYYYMMDDHHmm
            "threeDSReqPriorRef" => "", // Should be empty string or a valid 36-char UUID if available
            "threeDSReqPriorAuthData" => "" // Optional: Add if you have specific auth data
        ],
        "acctID" => (string)$customer->id,
        "merchantRiskIndicator" => [
            "deliveryEmailAddress" => $customer->email ?? "noreply@example.com",
            "deliveryTimeframe" => "ElectronicDelivery", // or "SameDayShipping", "OvernightShipping", "TwoDayOrMoreShipping"
            "giftCardAmount" => "0", // Must be string, 0 for non-gift card purchases
            "giftCardCount" => "0", // Must be string, 0 for non-gift card purchases
            "preOrderDate" => "", // Format: YYYYMMDD, empty if not a pre-order
            "preOrderPurchaseInd" => "MerchandiseAvailable", // or "FutureAvailability"
            "reorderItemsInd" => "FirstTimeOrdered", // or "Reordered"
            "shipIndicator" => "CardholderBillingAddress" // or "OtherVerifiedAddress", "DifferentToCardholderBillingAddress", etc.
        ]
    ],
];


        // Store initial payment attempt
        $payment = Payment::create([
            'order_id'         => $order->id,
            'transaction_type' => 'Payment',
            'vendor_tx_code'   => $vendorTxCode,
            'amount'           => $order->amount,
            'currency'         => $order->currency ?? "GBP",
            'raw_request'      => $payload
        ]);

        // Call Opayo
        Log::info('Opayo: Before create transaction');
        $resp = $this->opayo->createTransaction($payload);
        Log::info('Opayo: After create transaction');

        // Safely parse JSON
        $body = [];
        try {
            $body = $resp->json();
        } catch (\Exception $e) {
            Log::error('Opayo: Failed to parse JSON response', [
                'body' => $resp->body(),
                'exception' => $e->getMessage()
            ]);
            $body = ['error' => true, 'message' => 'Invalid response from payment gateway', 'raw' => $resp->body()];
        }

        $payment->raw_response = $resp->body();
        $payment->status = $resp->status();

        // Update payment and order based on 3DS requirements
        if (isset($body['3DSecure']) && $body['3DSecure']['status'] === 'Authenticated') {
            $order->update(['status' => 'paid']);
            $appointment->update(['payment_status' => 'paid']);
            $payment->requires_3ds = false;
        } elseif (isset($body['3DSecure']) && $body['3DSecure']['status'] === 'NotChecked') {
            $payment->requires_3ds = true;
            $payment->three_ds_data = $body;
        } else {
            $order->update(['status' => 'payment_failed']);
            $appointment->update(['payment_status' => 'failed']);
        }

        $appointment->save();
        $payment->save();

        return response()->json([
            'status' => $resp->status(),
            'body'   => $body
        ], $resp->status());
    }

    /**
     * Handle 3DS notification callback from the bank
     */
    public function handle3DSNotification(Request $r)
    {
        $data = $r->validate([
            'cRes' => 'required|string',
            'transactionId' => 'required|string'
        ]);

        Log::info('Opayo: 3DS Notification received', $data);

        $resp = $this->opayo->submit3DSecureChallenge($data['transactionId'], $data['cRes']);

        $body = [];
        try {
            $body = $resp->json();
        } catch (\Exception $e) {
            Log::error('Opayo: Failed to parse 3DS response', [
                'body' => $resp->body(),
                'exception' => $e->getMessage()
            ]);
            $body = ['error' => true, 'message' => 'Invalid 3DS response', 'raw' => $resp->body()];
        }

        // Update Payment status based on 3DS response
        $payment = Payment::where('transaction_id', $data['transactionId'])->first();
        if ($payment) {
            $payment->raw_response .= "\n3DS Challenge Response: " . json_encode($body);
            if (isset($body['3DSecure']) && $body['3DSecure']['status'] === 'Authenticated') {
                $payment->status = 'Authenticated';
                $order = $payment->order;
                $appointment = $order->appointment;
                $order->update(['status' => 'paid']);
                $appointment->update(['payment_status' => 'paid']);
                $appointment->save();
            } else {
                $payment->status = '3DS Failed';
            }
            $payment->save();
        }

        return response()->json([
            'status' => $resp->status(),
            'body' => $body
        ], $resp->status());
    }








    // 4) Order status
    public function orderStatus(Order $order)
    {
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'payments' => $order->payments()->latest()->get()
        ]);
    }

    // 5) Refund
    public function refund(Request $r, Order $order)
    {
        $this->validate($r, ['amount'=>'required|numeric']);
        $amountPence = intval(round($r->amount * 100));

        // find last successful transaction id
        $last = $order->payments()->whereNotNull('transaction_id')->latest()->first();
        if (!$last) return response()->json(['error'=>'No transaction to refund'], 422);

        $payload = [
            'transactionType' => 'Refund',
            'relatedTransactionId' => $last->transaction_id,
            'vendorTxCode' => 'refund-'.$order->id.'-'.uniqid(),
            'amount' => $amountPence,
            'currency' => $order->currency
        ];

        $resp = $this->opayo->createTransaction($payload);

        // store refund as a Payment record
        $refund = Payment::create([
            'order_id' => $order->id,
            'transaction_type' => 'Refund',
            'vendor_tx_code' => $payload['vendorTxCode'],
            'amount' => $amountPence,
            'currency' => $order->currency,
            'raw_request' => $payload,
            'raw_response' => $resp->body(),
            'status' => $resp->json('status') ?? $resp->status(),
            'transaction_id' => $resp->json('transactionId') ?? null
        ]);

        if ($resp->status() == 201) {
            $order->update(['status' => 'refunded']);
        }

        return response()->json(['status'=>$resp->status(),'body'=>$resp->json()], $resp->status());
    }

    // Optional return view after payment
    public function paymentReturn(Request $r) {
        return view('payment-return', ['query'=>$r->all()]);
    }

    
}

