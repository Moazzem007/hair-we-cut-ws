<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpayoService;
use App\Models\PaymentOrders as Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    // 1) Create Order (API)
    public function createOrder(Request $r)
    {
        $data = $r->validate(['amount'=>'required|numeric','currency'=>'required','reference'=>'nullable|string']);
        // create local order (simplified)
        $order = Order::create([
            'reference' => $data['reference'] ?? 'ORD-'.time(),
            'amount' => intval(round($data['amount'] * 100)),
            'currency' => $data['currency']
        ]);

        return response()->json([
            'order_id' => $order->id,
            'checkout_url' => url('/checkout/'.$order->id)
        ], 201);
    }

    // 2) Checkout page (webview) — returns Blade with merchantSessionKey
    public function checkoutPage(Order $order)
    {
        // create merchantSessionKey server-side
        $resp = $this->opayo->createMerchantSessionKey(env('OPAYO_VENDOR'));
        if ($resp->failed()) {
            \Log::error('MSK failed', ['status'=>$resp->status(),'body'=>$resp->body()]);
            abort(500, 'Payment provider error');
        }
        $body = $resp->json();
        $merchantSessionKey = $body['merchantSessionKey'] ?? null;
        return view('checkout', compact('order','merchantSessionKey'));
    }

    // 3) Register transaction: backend receives cardIdentifier from drop-in
    public function registerTransaction(Request $r)
    {
        $data = $r->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'merchantSessionKey' => 'required|string',
            'cardIdentifier' => 'required|string'
        ]);

        $order = Order::findOrFail($data['order_id']);

        $vendorTxCode = 'order-'.$order->id.'-'.uniqid();

        $payload = [
            'transactionType' => 'Payment',
            'vendorTxCode' => $vendorTxCode,
            'amount' => $order->amount, // in pence
            'currency' => $order->currency,
            'paymentMethod' => [
                'card' => [
                    'merchantSessionKey' => $data['merchantSessionKey'],
                    'cardIdentifier' => $data['cardIdentifier'],
                    'reusable' => false
                ]
            ],
            'customerEmail' => $order->customer_email ?? null,
            'customerPhone' => $order->customer_phone ?? null
        ];

        // store initial payment attempt
        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_type' => 'Payment',
            'vendor_tx_code' => $vendorTxCode,
            'amount' => $order->amount,
            'currency' => $order->currency,
            'raw_request' => $payload
        ]);

        $resp = $this->opayo->createTransaction($payload);

        $payment->raw_response = $resp->body();
        $payment->status = $resp->json('status') ?? $resp->status();
        if ($resp->status() == 201) {
            $body = $resp->json();
            $payment->transaction_id = $body['transactionId'] ?? null;
            $payment->requires_3ds = false;
            $order->update(['status' => 'paid']);
        } elseif ($resp->status() == 202) {
            // 3DS required — response contains paReq/md/acsUrl or 3DSv2 elements
            $body = $resp->json();
            $payment->requires_3ds = true;
            $payment->three_ds_data = $body; // store whole body for processing
            // return to frontend so drop-in can handle challenge if using hosted drop-in
        } else {
            // failed
            $order->update(['status' => 'payment_failed']);
        }
        $payment->save();

        return response()->json(['status'=>$resp->status(),'body'=>$resp->json()], $resp->status());
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
