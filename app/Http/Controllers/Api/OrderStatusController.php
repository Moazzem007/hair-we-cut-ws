<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PaymentOrders;
use App\Services\OpayoService;

class OrderStatusController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    public function show($orderId)
    {
        $order = PaymentOrders::findOrFail($orderId);
        if ($order->opayo_transaction_id && $order->status === 'pending') {
            // fetch latest status from Opayo
            try {
                $tx = $this->opayo->retrieveTransaction($order->opayo_transaction_id);
                $status = strtolower($tx['status'] ?? '');
                if (in_array($status, ['ok','ok'])) { $order->update(['status' => 'paid', 'opayo_response' => $tx]); }
                elseif (in_array($status, ['rejected','notauthed','error'])) { $order->update(['status'=>'failed','opayo_response'=>$tx]); }
            } catch (\Throwable $e) {
                // swallow — we'll still return local status
            }
            $order->refresh();
        }

        return response()->json(['id'=>$order->id,'status'=>$order->status,'opayo_response'=>$order->opayo_response]);
    }
}
