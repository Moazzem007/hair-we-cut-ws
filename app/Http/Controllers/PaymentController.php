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
                    "reusable"           => false
                ]
            ],
            "customerFirstName" => $customer->name ?? "Customer",
            "customerLastName"  => "Name",
            "billingAddress" => [
                "address1"   => $customer->billing_address,
                "city"       => "N/A",
                "postalCode" => $customer->postal_code,
                "country"    => "GB"
            ],
            "customerEmail" => $customer->email ?? "unknown@example.com",
            "customerPhone" => $customer->contact ?? null,
            "apply3DSecure" => "Force",
            "strongCustomerAuthentication" => [
    // Required fields
    "notificationURL" => url('/3ds-notification'), // Must be HTTPS
    "browserIP" => $r->ip() ?: '1.1.1.1', // Fallback IP if not available
    "browserAcceptHeader" => $r->header('Accept') ?? 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
    "browserUserAgent" => $r->header('User-Agent') ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    "browserJavaEnabled" => true,
    "browserJavascriptEnabled" => true, // Required for 3DS2
    "browserLanguage" => substr($r->header('Accept-Language', 'en-GB'), 0, 8),
    "browserColorDepth" => (string)($r->input('browserColorDepth') ?? 24), // Must be string
    "browserScreenHeight" => (string)($r->input('browserScreenHeight') ?? '1080'), // Must be string
    "browserScreenWidth" => (string)($r->input('browserScreenWidth') ?? '1920'), // Must be string
    "browserTZ" => (string)($r->input('browserTZ') ?? '0'), // Timezone offset in minutes, must be string
    "browserChallengeWindowSize" => "Small", // Small=250x400, Medium=390x400, FullScreen=fullscreen
    "challengeWindowSize" => "Small", // For backward compatibility
    "transType" => "GoodsAndServicePurchase", // Or "CheckAcceptance", "AccountFunding", "QuasiCash", "PrePaidVacation"
    "threeDSRequestorChallengeInd" => "01", // 01 = No preference, 02 = No challenge requested, 03 = Challenge requested (3DS Requestor preference), 04 = Challenge requested (Mandate)
    "threeDSRequestorAuthenticationInd" => "01", // 01 = Payment transaction, 02 = Recurring transaction, 03 = Installment transaction, 04 = Add card, 05 = Maintain card, 06 = Cardholder verification, 07 = Billing agreement, 08 = Split/delayed shipment, 09 = Split shipment, 10 = Top-up, 11 = Mail order, 12 = Telephone order, 13 = Whitelist status check, 14 = Other payment, 15 = Billing, 16 = Maintenance, 17 = No show, 18 = Re-authorisation, 19 = Resubmission, 20 = Delayed charge, 21 = Re-presentment, 22 = Repeat transaction, 23 = Cardholder account verification, 24 = Split shipment, 25 = Subscription, 26 = Terms and conditions acceptance, 27 = Installment, 28 = Update card, 29 = Create card, 30 = Token, 31 = Token update, 32 = Token delete, 33 = Token status change, 34 = Account verification, 35 = Account creation, 36 = Account update, 37 = Account closure, 38 = Account check, 39 = Account funding, 40 = Account funding from, 41 = Account funding to, 42 = Payment method update, 43 = Payment method token update, 44 = Payment method token delete, 45 = Payment method token status change, 46 = Recurring payment, 47 = Recurring payment update, 48 = Recurring payment delete, 49 = Recurring payment status change, 50 = Recurring payment cancellation, 51 = Recurring payment suspension, 52 = Recurring payment reactivation, 53 = Recurring payment completion, 54 = Recurring payment failure, 55 = Recurring payment success, 56 = Recurring payment cancellation request, 57 = Recurring payment suspension request, 58 = Recurring payment reactivation request, 59 = Recurring payment completion request, 60 = Recurring payment failure request, 61 = Recurring payment success request, 62 = Recurring payment cancellation response, 63 = Recurring payment suspension response, 64 = Recurring payment reactivation response, 65 = Recurring payment completion response, 66 = Recurring payment failure response, 67 = Recurring payment success response, 68 = Recurring payment cancellation notification, 69 = Recurring payment suspension notification, 70 = Recurring payment reactivation notification, 71 = Recurring payment completion notification, 72 = Recurring payment failure notification, 73 = Recurring payment success notification, 74 = Recurring payment cancellation confirmation, 75 = Recurring payment suspension confirmation, 76 = Recurring payment reactivation confirmation, 77 = Recurring payment completion confirmation, 78 = Recurring payment failure confirmation, 79 = Recurring payment success confirmation, 80 = Recurring payment cancellation rejection, 81 = Recurring payment suspension rejection, 82 = Recurring payment reactivation rejection, 83 = Recurring payment completion rejection, 84 = Recurring payment failure rejection, 85 = Recurring payment success rejection, 86 = Recurring payment cancellation error, 87 = Recurring payment suspension error, 88 = Recurring payment reactivation error, 89 = Recurring payment completion error, 90 = Recurring payment failure error, 91 = Recurring payment success error, 92 = Recurring payment cancellation timeout, 93 = Recurring payment suspension timeout, 94 = Recurring payment reactivation timeout, 95 = Recurring payment completion timeout, 96 = Recurring payment failure timeout, 97 = Recurring payment success timeout, 98 = Recurring payment cancellation unknown, 99 = Recurring payment suspension unknown, 100 = Recurring payment reactivation unknown, 101 = Recurring payment completion unknown, 102 = Recurring payment failure unknown, 103 = Recurring payment success unknown
    "threeDSRequestorAuthenticationInfo" => [
        "threeDSReqAuthMethod" => "01", // 01 = No 3DS Requestor authentication occurred, 02 = Login to the cardholder account at the 3DS Requestor system, 03 = Login to the cardholder account at the 3DS Requestor system using credentials, 04 = Login to the cardholder account at the 3DS Requestor system using federated ID, 05 = Login to the cardholder account at the 3DS Requestor system using issuer credentials, 06 = Login to the cardholder account at the 3DS Requestor system using third-party authentication, 07 = Login to the cardholder account at the 3DS Requestor system using FIDO Authenticator, 08 = Login to the cardholder account at the 3DS Requestor system using FIDO Authenticator (FIDO Alliance) score, 09 = Login to the cardholder account at the 3DS Requestor system using FIDO Authenticator (EMVCo.) score, 10-79 = Reserved for EMVCo. use, 80-99 = Reserved for DS use
        "threeDSReqAuthTimestamp" => now()->toIso8601String(), // Format: YYYYMMDDHHmm
        "threeDSReqAuthData" => "fido" // Additional authentication data
    ],
    "acctID" => (string)$customer->id, // Must be string
    "merchantRiskIndicator" => [
        "deliveryEmailAddress" => $customer->email ?? "noreply@example.com",
        "deliveryTimeframe" => "01", // 01 = Electronic Delivery, 02 = Same day shipping, 03 = Overnight shipping, 04 = Two-day or more shipping
        "giftCardAmount" => 0, // Must be 0 for non-gift card purchases
        "giftCardCount" => 0, // Must be 0 for non-gift card purchases
        "giftCardCurrency" => "GBP", // Must be set if giftCardAmount > 0
        "preOrderDate" => "", // Format: YYYY-MM-DD
        "preOrderPurchaseInd" => "01", // 01 = Merchandise available, 02 = Future availability
        "reorderItemsInd" => "01", // 01 = First time ordered, 02 = Reordered
        "shipIndicator" => "01" // 01 = Ship to cardholder's billing address, 02 = Ship to another verified address on file with merchant, 03 = Ship to address that is different than the cardholder's billing address, 04 = "Ship to Store" / Pick-up at local store (Store address shall be populated in shipping address fields), 05 = Digital goods (includes online services, electronic gift cards and redemption codes), 06 = Travel and Event tickets, not shipped, 07 = Other (for example, Gaming, digital services not shipped, emedia subscriptions, etc.)
    ],
    "threeDSRequestorPriorAuthenticationInfo" => [
        "threeDSReqPriorAuthMethod" => "01", // 01 = Frictionless authentication occurred, 02 = Cardholder challenge occurred, 03 = AVS verified, 04 = Other issuer methods
        "threeDSReqPriorAuthTimestamp" => now()->subHours(1)->toIso8601String(), // Format: YYYYMMDDHHmm
        "threeDSReqPriorRef" => "" // Required if threeDSReqPriorAuthMethod is 01-03
    ],
    "threeDSRequestorChallengeInd" => "01", // 01 = No preference, 02 = No challenge requested, 03 = Challenge requested (3DS Requestor preference), 04 = Challenge requested (Mandate)
    "sdkMaxTimeout" => "05", // 05 = 5 minutes (max allowed)
    "sdkEphemPubKey" => [
        "kty" => "EC",
        "crv" => "P-256",
        "x" => "MKBCTNIcKUSDii11ySs3526iDZ8AiTo7Tu6KPAqv7D4",
        "y" => "4Etl6SRW2YiLUrN5vfvVHuhp7x8PxltmWWlbbM4IFyM"
    ],
    "sdkReferenceNumber" => "3DS_LOA_SDK_PPFU_020100_00007",
    "sdkTransID" => (string) \Illuminate\Support\Str::uuid(),
    "sdkInterface" => "01", // 01 = Native, 02 = HTML, 03 = Both
    "sdkUiType" => ["01", "02", "03", "04", "05"], // 01 = Text, 02 = Single select, 03 = Multi-select, 04 = OOB, 05 = HTML
    "accountInfo" => [
        "chAccAgeInd" => "01", // 01 = No account (guest check-out), 02 = Created during this transaction, 03 = Less than 30 days, 04 = 30-60 days, 05 = More than 60 days
        "chAccChange" => "01", // 01 = Changed during this transaction, 02 = Less than 30 days, 03 = 30-60 days, 04 = More than 60 days
        "chAccChangeInd" => "01", // 01 = Changed during this transaction, 02 = Less than 30 days, 03 = 30-60 days, 04 = More than 60 days
        "chAccDate" => now()->subDays(10)->format('Ymd'), // Format: YYYYMMDD
        "chAccPwChange" => "01", // 01 = No change, 02 = Changed during this transaction, 03 = Less than 30 days, 04 = 30-60 days, 05 = More than 60 days
        "chAccPwChangeInd" => "01", // 01 = No change, 02 = Changed during this transaction, 03 = Less than 30 days, 04 = 30-60 days, 05 = More than 60 days
        "nbPurchaseAccount" => "1", // Number of purchases with this cardholder account in the last 6 months
        "provisionAttemptsDay" => "1", // Number of Add Card attempts in the last 24 hours
        "txnActivityDay" => "1", // Number of transactions with this cardholder account across all payment accounts in the last 24 hours
        "txnActivityYear" => "1", // Number of transactions with this cardholder account across all payment accounts in the last year
        "paymentAccAge" => "01", // 01 = No account (guest check-out), 02 = Created during this transaction, 03 = Less than 30 days, 04 = 30-60 days, 05 = More than 60 days
        "paymentAccInd" => "01", // 01 = No account (guest check-out), 02 = Created during this transaction, 03 = Less than 30 days, 04 = 30-60 days, 05 = More than 60 days
        "shipAddressUsage" => "01", // 01 = This transaction, 02 = Less than 30 days, 03 = 30-60 days, 04 = More than 60 days
        "shipAddressUsageInd" => "01", // 01 = This transaction, 02 = Less than 30 days, 03 = 30-60 days, 04 = More than 60 days
        "shipNameIndicator" => "01", // 01 = Account name identical to shipping name, 02 = Account name different than shipping name
        "suspiciousAccActivity" => "01", // 01 = No suspicious activity has been observed, 02 = Suspicious activity has been observed
    ]
]
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

