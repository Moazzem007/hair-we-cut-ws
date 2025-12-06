<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\OpayoService;
use App\Models\PaymentOrders as Order;
use App\Models\Payment;

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
        $resp = $this->opayo->createMerchantSessionKey("paymentsavetest");
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
    // Validate incoming request
    $data = $r->validate([
        'appointment_id' => 'required|integer|exists:appointments,id',
        'order_id' => 'required|integer|exists:orders,id',
        'merchantSessionKey' => 'required|string',
        'cardIdentifier' => 'required|string'
    ]);

    $order = Order::findOrFail($data['order_id']);
    $appointment = Appointment::findOrFail($data['appointment_id']);
    if(!$appointment){
        return response()->json([
            'status' => 422,
            'body' => ['errors' => [['description' => 'Invalid appointment id', 'property' => 'appointment_id', 'code' => 1016]]]
        ], 422);
    }
    $customer = Customer::findOrFail($appointment->customer_id);
    if(!$customer){
        return response()->json([
            'status' => 422,
            'body' => ['errors' => [['description' => 'Invalid customer id', 'property' => 'customer_id', 'code' => 1016]]]
        ], 422);
    }

    if(!$customer->postal_code){
        return response()->json([
            'status' => 422,
            'body' => ['errors' => [['description' => 'Postal code not found', 'property' => 'postal_code', 'code' => 1016]]]
        ], 422);
    }

    if(!$customer->billing_address){
        return response()->json([
            'status' => 422,
            'body' => ['errors' => [['description' => 'Billing address not found', 'property' => 'billing_address', 'code' => 1016]]]
        ], 422);
    }



    // Ensure amount is numeric and positive
    if (!is_numeric($order->amount) || $order->amount <= 0) {
        return response()->json([
            'status' => 422,
            'body' => ['errors' => [['description' => 'Invalid order amount', 'property' => 'amount', 'code' => 1016]]]
        ], 422);
    }

    // Generate unique vendorTxCode
    $vendorTxCode = 'order-' . $order->id . '-' . uniqid();

    /**
     * Convert amount to pence (integer)
     */
    $amountInPence = (int) round(floatval($order->amount) * 100);

    $payload = [
        "transactionType" => "Payment",
        "vendorTxCode"    => $vendorTxCode,
        "amount" => $amountInPence,
        "currency" => "GBP",
        "description" => "Order #{$order->id} payment",
        "paymentMethod" => [
            "card" => [
                "merchantSessionKey" => $data['merchantSessionKey'],
                "cardIdentifier"     => $data['cardIdentifier'],
                "reusable"           => false
            ]
        ],
        // Move customer fields to root level
        "customerFirstName" => $customer->name ?? "Customer",
        "customerLastName"  => "Name",
        "billingAddress" => [
            "address1"    => $customer->billing_address,
            "city"        => "N/A",
            "postalCode"  => $customer->postal_code,
            "country"     => "GB"
        ],
        // Keep email and phone at root level as well
        "customerEmail" => $customer->email ?? "unknown@example.com",
        "customerPhone" => $customer->contact ?? null
    ];

    /**
     * Store initial payment attempt
     */
    $payment = Payment::create([
        'order_id'         => $order->id,
        'transaction_type' => 'Payment',
        'vendor_tx_code'   => $vendorTxCode,
        'amount'           => $order->amount,
        'currency'         => $order->currency ?? "GBP",
        'raw_request'      => $payload
    ]);

    /**
     * Perform API call
     */
    $resp = $this->opayo->createTransaction($payload);

    $payment->raw_response = $resp->body();
    $payment->status = $resp->json('status') ?? $resp->status();

    /**
     * Handle API responses
     */
    if ($resp->status() == 201) { // Payment SUCCESS
        $body = $resp->json();
        $payment->transaction_id = $body['transactionId'] ?? null;
        $payment->requires_3ds = false;

        $order->update(['status' => 'paid']);
        $appointment->update(['payment_status' => 'paid']);
    } elseif ($resp->status() == 202) { // 3D SECURE REQUIRED
        $body = $resp->json();
        $payment->requires_3ds = true;
        $payment->three_ds_data = $body;
        // Order is NOT marked as paid yet
    } else { // Other → FAILED
        $order->update(['status' => 'payment_failed']);
        $appointment->update(['payment_status' => 'failed']);
    }

    $appointment->save();
    $payment->save();

    return response()->json([
        'status' => $resp->status(),
        'body'   => $resp->json()
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

