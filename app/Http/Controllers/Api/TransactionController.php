<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PaymentOrders;
use App\Services\OpayoService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    // receives { order_id, cardIdentifier, merchantSessionKey } from checkout page JS
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'cardIdentifier' => 'required|string',
            'merchantSessionKey' => 'required|string',
            'amount' => 'nullable|numeric'
        ]);

        $order = PaymentOrders::findOrFail($data['order_id']);

        $payload = [
            'transactionType' => 'Payment',
            'vendorTxCode' => 'order-' . $order->id . '-' . time(),
            'amount' => $order->amount, // Opayo expects integer smallest unit (pence)
            'currency' => $order->currency,
            'description' => "Order #{$order->id}",
            'paymentMethod' => [
                'card' => [
                    'merchantSessionKey' => $data['merchantSessionKey'],
                    'cardIdentifier' => $data['cardIdentifier'],
                    'reusable' => false
                ]
            ],
            'customerEmail' => $request->user()?->email ?? 'customer@example.com',
            'customerPhone' => '0000000000',
        ];

        $resp = $this->opayo->createTransaction($payload);

        // If 201 -> immediate result; 202 -> needs redirect (3DS)
        if ($resp->status() == 201) {
            $body = $resp->json();
            $order->update([
                'status' => in_array(strtolower($body['status'] ?? ''), ['ok','ok']) ? 'paid' : 'failed',
                'opayo_transaction_id' => $body['transactionId'] ?? ($body['transactionId'] ?? null),
                'opayo_response' => $body
            ]);
            return response()->json($body, 201);
        } elseif ($resp->status() == 202) {
            // 3DS: response contains acsUrl, paReq etc. Return details to client to redirect
            $body = $resp->json();
            // Save ephemeral response
            $order->update(['opayo_response' => $body]);
            return response()->json($body, 202);
        } else {
            return response()->json(['error'=>'transaction failed','details'=>$resp->body()], $resp->status());
        }
    }
}
