<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PaymentOrders;
use App\Services\OpayoService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    public function refund(Request $request, $orderId)
    {
        $order = PaymentOrders::findOrFail($orderId);
        if (!$order->opayo_transaction_id) {
            return response()->json(['error'=>'no transaction id found'], 400);
        }
        $amount = $request->input('amount', $order->amount); // integer pence

        // Opayo supports Refund transaction schema on /transactions
        $payload = [
            'transactionType' => 'Refund',
            'vendorTxCode' => 'refund-' . $order->id . '-' . time(),
            'amount' => intval($amount),
            'currency' => $order->currency,
            'relatedTransactionId' => $order->opayo_transaction_id,
            'description' => 'Refund for cancelled order ' . $order->id
        ];

        $resp = $this->opayo->createTransaction($payload);
        $body = $resp->json();
        if ($resp->status() === 201) {
            $order->update(['status' => 'refunded', 'opayo_response' => $body]);
            return response()->json($body, 201);
        } else {
            return response()->json(['error'=>'refund failed','details'=>$body], $resp->status());
        }
    }
}
