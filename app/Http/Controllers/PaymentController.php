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
    
    public function __construct(OpayoService $opayo) 
    { 
        $this->opayo = $opayo; 
    }

    /**
     * Create Payment Order (API)
     */
    public function createPaymentOrder(Request $r)
    {
        $data = $r->validate([
            'appointment_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'reference' => 'nullable|string'
        ]);

        $order = Order::create([
            'reference' => $data['reference'] ?? 'ORD-' . time(),
            'amount' => intval(round($data['amount'] * 100)),
            'currency' => 'GBP'
        ]);

        $appointment = Appointment::find($data['appointment_id']);
        $appointment->payment_status = 'pending';
        $appointment->save();

        return response()->json([
            'order_id' => $order->id,
            'checkout_url' => url('/checkout/' . $order->id . '/' . $data['appointment_id'])
        ], 201);
    }

    /**
     * Checkout page - returns Blade with merchantSessionKey
     */
    public function checkoutPage(Order $order, Appointment $appointment)
    {
        $resp = $this->opayo->createMerchantSessionKey();
        
        if ($resp->failed()) {
            Log::error('MSK generation failed', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            abort(500, 'Payment provider error');
        }
        
        $body = $resp->json();
        $merchantSessionKey = $body['merchantSessionKey'] ?? null;
        
        return view('checkout', compact('order', 'merchantSessionKey', 'appointment'));
    }

    /**
     * Register transaction - backend receives cardIdentifier from drop-in
     */
    public function registerTransaction(Request $r)
    {
        Log::info('Opayo: Register transaction called');

        $data = $r->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
            'order_id' => 'required|integer|exists:orders,id',
            'merchantSessionKey' => 'required|string',
            'cardIdentifier' => 'required|string',
            'strongCustomerAuthentication' => 'required|array'
        ]);

        $order = Order::findOrFail($data['order_id']);
        $appointment = Appointment::findOrFail($data['appointment_id']);
        $customer = Customer::findOrFail($appointment->customer_id);

        // Validate customer fields
        if (!$customer->postal_code || !$customer->billing_address) {
            return response()->json([
                'status' => 422,
                'body' => [
                    'errors' => [[
                        'description' => !$customer->postal_code ? 'Postal code required' : 'Billing address required',
                        'code' => 1016
                    ]]
                ]
            ], 422);
        }

        // Validate amount
        if (!is_numeric($order->amount) || $order->amount <= 0) {
            return response()->json([
                'status' => 422,
                'body' => [
                    'errors' => [[
                        'description' => 'Invalid order amount',
                        'code' => 1016
                    ]]
                ]
            ], 422);
        }

        $vendorTxCode = 'order-' . $order->id . '-' . uniqid();
        $amountInPence = (int) round(floatval($order->amount));

        // Build transaction payload
        $payload = [
            'transactionType' => 'Payment',
            'vendorTxCode' => $vendorTxCode,
            'amount' => $amountInPence,
            'currency' => 'GBP',
            'description' => "Order #{$order->id} payment",
            'paymentMethod' => [
                'card' => [
                    'merchantSessionKey' => $data['merchantSessionKey'],
                    'cardIdentifier' => $data['cardIdentifier'],
                    'reusable' => false,
                    'save' => false,
                ],
            ],
            'customerFirstName' => $customer->name ?? 'Customer',
            'customerLastName' => 'Name',
            'customerEmail' => $customer->email ?? 'customer@example.com',
            'customerPhone' => $customer->contact ?? null,
            'billingAddress' => [
                'address1' => $customer->billing_address,
                'city' => $customer->city ?? 'London',
                'postalCode' => $customer->postal_code,
                'country' => 'GB',
            ],
            'apply3DSecure' => 'Force',
            'applyAvsCvcCheck' => 'Disable',
            'strongCustomerAuthentication' => $data['strongCustomerAuthentication']
        ];

        // Store initial payment
        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_type' => 'Payment',
            'vendor_tx_code' => $vendorTxCode,
            'amount' => $amountInPence,
            'currency' => 'GBP',
            'raw_request' => json_encode($payload)
        ]);

        // Call Opayo
        Log::info('Opayo: Calling createTransaction');
        $resp = $this->opayo->createTransaction($payload);

        // Parse response
        $body = [];
        try {
            $body = $resp->json();
        } catch (\Exception $e) {
            Log::error('Opayo: Failed to parse response', [
                'body' => $resp->body(),
                'exception' => $e->getMessage()
            ]);
            $body = [
                'error' => true,
                'message' => 'Invalid response from gateway',
                'raw' => $resp->body()
            ];
        }

        // Update payment record
        $payment->raw_response = $resp->body();
        $payment->status = $resp->status();
        $payment->transaction_id = $body['transactionId'] ?? null;

        // Handle 3DS status
        if (isset($body['status']) && $body['status'] === '3DAuth') {
            $payment->requires_3ds = true;
            $payment->three_ds_data = json_encode($body);
            Log::info('Opayo: 3DS authentication required', [
                'transactionId' => $body['transactionId'] ?? 'unknown'
            ]);
        } elseif (isset($body['status']) && $body['status'] === 'Ok') {
            $payment->requires_3ds = false;
            $order->update(['status' => 'paid']);
            $appointment->update(['payment_status' => 'paid']);
            Log::info('Opayo: Payment successful without 3DS');
        } else {
            $order->update(['status' => 'payment_failed']);
            $appointment->update(['payment_status' => 'failed']);
            Log::warning('Opayo: Payment failed', [
                'status' => $body['status'] ?? 'unknown'
            ]);
        }

        $payment->save();

        return response()->json([
            'status' => $resp->status(),
            'body' => $body
        ], $resp->status());
    }

    /**
     * Handle 3DS notification callback from bank
     */
    public function handle3DSNotification(Request $request)
    {
        Log::info('Opayo: 3DS Notification received', $request->all());

        $validated = $request->validate([
            'cres' => 'required|string',
            'threeDSSessionData' => 'required|string'
        ]);

        $sessionData = base64_decode($validated['threeDSSessionData']);
        Log::info('Opayo: Decoded session data', ['data' => $sessionData]);

        preg_match('/order_(\d+)/', $sessionData, $matches);
        $orderId = $matches[1] ?? null;

        if (!$orderId) {
            Log::error('Opayo: Invalid session data');
            return $this->redirectToFailure('Invalid session data');
        }

        $payment = Payment::where('order_id', $orderId)
            ->whereNotNull('transaction_id')
            ->latest()
            ->first();

        if (!$payment) {
            Log::error('Opayo: Payment not found', ['orderId' => $orderId]);
            return $this->redirectToFailure('Payment record not found');
        }

        Log::info('Opayo: Submitting cRes', [
            'transactionId' => $payment->transaction_id,
            'orderId' => $orderId
        ]);

        // Use OpayoService method
        $response = $this->opayo->submit3DSecureChallenge(
            $payment->transaction_id,
            $validated['cres']
        );

        if (!$response || !$response->successful()) {
            Log::error('Opayo: 3DS challenge failed');
            return $this->redirectToFailure('Unable to complete authentication');
        }

        $body = $response->json();
        
        Log::info('Opayo: 3DS response', [
            'status' => $body['status'] ?? 'unknown'
        ]);

        $payment->raw_response = ($payment->raw_response ?? '') . "\n3DS: " . json_encode($body);
        
        if (isset($body['status']) && $body['status'] === 'Ok') {
            $payment->status = 'completed';
            $payment->save();
            
            $order = $payment->order;
            $order->update(['status' => 'paid']);
            
            if ($order->appointment) {
                $order->appointment->update(['payment_status' => 'paid']);
            }
            
            Log::info('Opayo: Payment successful');
            return $this->redirectToSuccess($orderId);
        } else {
            $payment->status = '3ds_failed';
            $payment->save();
            
            Log::warning('Opayo: 3DS failed');
            return $this->redirectToFailure($body['statusDetail'] ?? 'Authentication failed');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order');
        if (!$orderId) return redirect('/');
        
        $order = Order::with('appointment')->find($orderId);
        if (!$order) return redirect('/');
        
        return view('payment.success', [
            'order' => $order,
            'appointment' => $order->appointment
        ]);
    }

    /**
     * Payment failed page
     */
    public function paymentFailed(Request $request)
    {
        return view('payment.failed', [
            'error' => $request->query('error', 'Payment failed')
        ]);
    }

    /**
     * Redirect helpers
     */
    private function redirectToSuccess($orderId)
    {
        $url = route('payment.success', ['order' => $orderId]);
        return response()->make("<!DOCTYPE html>
<html><head><meta charset='utf-8'><title>Success</title>
<meta http-equiv='refresh' content='1;url={$url}'>
<style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f0f9f0;}
.success{color:#28a745;font-size:24px;}</style></head>
<body><div class='success'>✓ Payment Successful!</div><p>Redirecting...</p>
<script>setTimeout(()=>window.location.href='{$url}',1000);</script></body></html>", 200)
        ->header('Content-Type', 'text/html');
    }

    private function redirectToFailure($message = 'Payment failed')
    {
        $url = route('payment.failed', ['error' => $message]);
        return response()->make("<!DOCTYPE html>
<html><head><meta charset='utf-8'><title>Failed</title>
<meta http-equiv='refresh' content='3;url={$url}'>
<style>body{font-family:sans-serif;text-align:center;padding:50px;background:#fff5f5;}
.error{color:#dc3545;font-size:24px;}</style></head>
<body><div class='error'>✗ Payment Failed</div><p>" . htmlspecialchars($message) . "</p>
<script>setTimeout(()=>window.location.href='{$url}',3000);</script></body></html>", 200)
        ->header('Content-Type', 'text/html');
    }

    /**
     * Refund
     */
    public function refund(Request $r, Order $order)
    {
        $this->validate($r, ['amount' => 'required|numeric']);
        $amountPence = intval(round($r->amount * 100));

        $last = $order->payments()->whereNotNull('transaction_id')->latest()->first();
        if (!$last) {
            return response()->json(['error' => 'No transaction to refund'], 422);
        }

        $payload = [
            'transactionType' => 'Refund',
            'relatedTransactionId' => $last->transaction_id,
            'vendorTxCode' => 'refund-' . $order->id . '-' . uniqid(),
            'amount' => $amountPence,
            'currency' => $order->currency
        ];

        $resp = $this->opayo->createTransaction($payload);

        $refund = Payment::create([
            'order_id' => $order->id,
            'transaction_type' => 'Refund',
            'vendor_tx_code' => $payload['vendorTxCode'],
            'amount' => $amountPence,
            'currency' => $order->currency,
            'raw_request' => json_encode($payload),
            'raw_response' => $resp->body(),
            'status' => $resp->json('status') ?? $resp->status(),
            'transaction_id' => $resp->json('transactionId') ?? null
        ]);

        if ($resp->status() == 201) {
            $order->update(['status' => 'refunded']);
        }

        return response()->json([
            'status' => $resp->status(),
            'body' => $resp->json()
        ], $resp->status());
    }

    /**
     * Order status
     */
    public function orderStatus(Order $order)
    {
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'payments' => $order->payments()->latest()->get()
        ]);
    }
}