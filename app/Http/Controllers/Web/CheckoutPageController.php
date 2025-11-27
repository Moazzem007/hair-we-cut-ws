<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\PaymentOrders;
use App\Services\OpayoService;

class CheckoutPageController extends Controller
{
    protected $opayo;
    public function __construct(OpayoService $opayo) { $this->opayo = $opayo; }

    public function show($orderId)
    {
        $order = PaymentOrders::findOrFail($orderId);

        // create merchant session key for the drop-in (expires ~400s)
        $mskResp = $this->opayo->createMerchantSessionKey(config('app.opayo_vendor', env('OPAYO_VENDOR')));
        $merchantSessionKey = $mskResp['merchantSessionKey'] ?? ($mskResp['merchantSessionKey'] ?? null);

        // render a simple blade with JS drop-in. Pass merchantSessionKey & order info
        return view('opayo.checkout', compact('order','merchantSessionKey'));
    }
}
