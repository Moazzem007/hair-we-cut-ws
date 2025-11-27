<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentOrders;
use App\Services\OpayoService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'reference' => 'required|string',
        ]);

        $order = PaymentOrders::create([
            'reference' => $data['reference'],
            'amount' => intval(round($data['amount'] * 100)), // store pence
            'currency' => strtoupper($data['currency']),
            'status' => 'pending',
        ]);

        // Build a checkout URL pointing to your web route
        $checkoutUrl = url("/checkout/{$order->id}");

        return response()->json(['order_id' => $order->id, 'checkout_url' => $checkoutUrl], 201);
    }
}
