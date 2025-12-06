<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\OpayoService;
use App\Models\PaymentOrders as Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $opayo;

    public function __construct(OpayoService $opayo)
    {
        $this->opayo = $opayo;
    }

    /**
     * 1) Create Order (API)
     */
    public function createPaymentOrder(Request $request)
    {
        Log::info('=== CREATE PAYMENT ORDER ===', $request->all());

        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|numeric|exists:appointments,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:40'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            // Create order
            $order = Order::create([
                'appointment_id' => $data['appointment_id'],
                'reference' => $data['reference'] ?? 'ORD-' . time(),
                'amount' => intval(round($data['amount'] * 100)), // Convert to pence
                'currency' => 'GBP',
                'status' => 'pending'
            ]);

            // Update appointment
            $appointment = Appointment::findOrFail($data['appointment_id']);
            $appointment->payment_status = 'pending';
            $appointment->save();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'appointment_id' => $appointment->id
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'checkout_url' => url('/checkout/' . $order->id . '/' . $data['appointment_id'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2) Checkout page (webview) — returns Blade with merchantSessionKey
     */
    public function checkoutPage(Order $order, Appointment $appointment)
    {
        Log::info('=== CHECKOUT PAGE ===', [
            'order_id' => $order->id,
            'appointment_id' => $appointment->id
        ]);

        // Verify order belongs to appointment
        if ($order->appointment_id != $appointment->id) {
            Log::error('Order/Appointment mismatch', [
                'order_appointment_id' => $order->appointment_id,
                'requested_appointment_id' => $appointment->id
            ]);
            abort(404, 'Order not found for this appointment');
        }

        // Check if already paid
        if ($order->status === 'paid') {
            Log::info('Order already paid, redirecting');
            return redirect()->url('myapp://payment-success?order_id=' . $order->id);
        }

        // Create merchant session key
        $vendorName = config('services.opayo.vendor_name', 'sandbox');

        Log::info('Creating Merchant Session Key', ['vendor' => $vendorName]);

        $response = $this->opayo->createMerchantSessionKey($vendorName);

        if ($response->failed()) {
            Log::error('MSK creation failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            abort(500, 'Payment provider error. Please try again.');
        }

        $body = $response->json();
        $merchantSessionKey = $body['merchantSessionKey'] ?? null;

        if (!$merchantSessionKey) {
            Log::error('MSK not found in response', ['response' => $body]);
            abort(500, 'Payment configuration error');
        }

        Log::info('MSK created successfully', [
            'msk' => substr($merchantSessionKey, 0, 20) . '...'
        ]);

        return view('checkout', compact('order', 'merchantSessionKey', 'appointment'));
    }

    /**
     * 3) Register transaction: backend receives cardIdentifier from drop-in
     */
    public function registerTransaction(Request $request)
    {
        Log::info('=== REGISTER TRANSACTION START ===');
        Log::info('Request Headers', $request->headers->all());
        Log::info('Request Body', $request->all());

        // Validate input
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'order_id' => 'required|integer|exists:payment_orders,id',
            'merchantSessionKey' => 'required|string',
            'cardIdentifier' => 'required|string'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());

            return response()->json([
                'status' => 422,
                'body' => [
                    'errors' => $validator->errors()->all()
                ]
            ], 422);
        }

        $data = $validator->validated();

        try {
            // Load models
            $order = Order::findOrFail($data['order_id']);
            $appointment = Appointment::findOrFail($data['appointment_id']);
            $customer = Customer::findOrFail($appointment->customer_id);

            Log::info('Models loaded', [
                'order_id' => $order->id,
                'appointment_id' => $appointment->id,
                'customer_id' => $customer->id
            ]);

            // Validate customer data
            if (!$customer->postal_code) {
                return response()->json([
                    'status' => 422,
                    'body' => [
                        'errors' => [
                            ['description' => 'Customer postal code is required', 'property' => 'postal_code']
                        ]
                    ]
                ], 422);
            }

            if (!$customer->billing_address) {
                return response()->json([
                    'status' => 422,
                    'body' => [
                        'errors' => [
                            ['description' => 'Customer billing address is required', 'property' => 'billing_address']
                        ]
                    ]
                ], 422);
            }

            // Check if already paid
            if ($order->status === 'paid') {
                Log::warning('Order already paid');
                return response()->json([
                    'status' => 400,
                    'body' => [
                        'errors' => [
                            ['description' => 'Order has already been paid']
                        ]
                    ]
                ], 400);
            }

            // Validate amount
            if (!is_numeric($order->amount) || $order->amount <= 0) {
                return response()->json([
                    'status' => 422,
                    'body' => [
                        'errors' => [
                            ['description' => 'Invalid order amount', 'property' => 'amount']
                        ]
                    ]
                ], 422);
            }

            // Generate unique vendorTxCode
            $vendorTxCode = 'order-' . $order->id . '-' . uniqid();

            // Amount must be in pence (already stored as pence in DB)
            $amountInPence = (int) $order->amount;

            Log::info('Building transaction payload', [
                'vendorTxCode' => $vendorTxCode,
                'amount' => $amountInPence
            ]);

            // Build Opayo transaction payload
            $payload = [
                'transactionType' => 'Payment',
                'vendorTxCode' => $vendorTxCode,
                'amount' => $amountInPence,
                'currency' => 'GBP',
                'description' => 'Appointment #' . $appointment->id . ' - Order #' . $order->id,
                'paymentMethod' => [
                    'card' => [
                        'merchantSessionKey' => $data['merchantSessionKey'],
                        'cardIdentifier' => $data['cardIdentifier'],
                        'reusable' => false,
                        'save' => false
                    ]
                ],
                'customerFirstName' => $customer->first_name ?? explode(' ', $customer->name)[0] ?? 'Customer',
                'customerLastName' => $customer->last_name ?? explode(' ', $customer->name)[1] ?? 'Name',
                'customerEmail' => $customer->email ?? 'customer@example.com',
                'customerPhone' => $customer->contact ?? '+441234567890',
                'billingAddress' => [
                    'address1' => $customer->billing_address,
                    'city' => $customer->city ?? 'London',
                    'postalCode' => $customer->postal_code,
                    'country' => 'GB'
                ],
                'entryMethod' => 'Ecommerce'
            ];

            Log::info('Transaction payload built', $payload);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'appointment_id' => $appointment->id,
                'transaction_type' => 'Payment',
                'vendor_tx_code' => $vendorTxCode,
                'amount' => $order->amount,
                'currency' => 'GBP',
                'status' => 'pending',
                'raw_request' => json_encode($payload)
            ]);

            Log::info('Payment record created', ['payment_id' => $payment->id]);

            // Call Opayo API
            Log::info('Calling Opayo API...');
            $response = $this->opayo->createTransaction($payload);

            Log::info('Opayo API response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Update payment with response
            $payment->raw_response = $response->body();
            $payment->status = $response->status();

            $responseBody = $response->json();

            // Handle different response codes
            if ($response->status() == 201) {
                // SUCCESS
                Log::info('=== PAYMENT SUCCESSFUL ===');

                $payment->transaction_id = $responseBody['transactionId'] ?? null;
                $payment->requires_3ds = false;
                $payment->status = 'completed';
                $payment->save();

                $order->update([
                    'status' => 'paid',
                    'transaction_id' => $payment->transaction_id
                ]);

                $appointment->update(['payment_status' => 'paid']);

                Log::info('Payment completed', [
                    'transaction_id' => $payment->transaction_id
                ]);

                return response()->json([
                    'status' => 201,
                    'body' => $responseBody
                ], 201);

            } elseif ($response->status() == 202) {
                // 3D SECURE REQUIRED
                Log::info('=== 3DS REQUIRED ===');

                $payment->requires_3ds = true;
                $payment->three_ds_data = json_encode($responseBody);
                $payment->save();

                return response()->json([
                    'status' => 202,
                    'body' => $responseBody
                ], 202);

            } else {
                // FAILED
                Log::warning('=== PAYMENT FAILED ===', [
                    'status' => $response->status(),
                    'statusDetail' => $responseBody['statusDetail'] ?? 'Unknown'
                ]);

                $payment->status = 'failed';
                $payment->save();

                $order->update(['status' => 'payment_failed']);
                $appointment->update(['payment_status' => 'failed']);

                return response()->json([
                    'status' => $response->status(),
                    'body' => $responseBody
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('=== TRANSACTION ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 500,
                'body' => [
                    'errors' => [
                        ['description' => 'Internal server error: ' . $e->getMessage()]
                    ]
                ]
            ], 500);
        }
    }

    /**
     * 4) Order status
     */
    public function orderStatus(Order $order)
    {
        Log::info('Getting order status', ['order_id' => $order->id]);

        return response()->json([
            'id' => $order->id,
            'reference' => $order->reference,
            'status' => $order->status,
            'amount' => $order->amount,
            'currency' => $order->currency,
            'payments' => $order->payments()->latest()->get()
        ]);
    }

    /**
     * 5) Refund
     */
    public function refund(Request $request, Order $order)
    {
        Log::info('=== REFUND REQUEST ===', [
            'order_id' => $order->id,
            'amount' => $request->amount
        ]);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->errors()
            ], 422);
        }

        $amountPence = intval(round($request->amount * 100));

        // Find last successful transaction
        $lastPayment = $order->payments()
            ->whereNotNull('transaction_id')
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$lastPayment) {
            return response()->json([
                'error' => true,
                'message' => 'No successful transaction to refund'
            ], 422);
        }

        // Build refund payload
        $payload = [
            'transactionType' => 'Refund',
            'referenceTransactionId' => $lastPayment->transaction_id,
            'vendorTxCode' => 'refund-' . $order->id . '-' . uniqid(),
            'amount' => $amountPence,
            'description' => 'Refund for Order #' . $order->id
        ];

        Log::info('Refund payload', $payload);

        // Call Opayo API
        $response = $this->opayo->createTransaction($payload);

        // Create refund payment record
        $refund = Payment::create([
            'order_id' => $order->id,
            'appointment_id' => $order->appointment_id,
            'transaction_type' => 'Refund',
            'vendor_tx_code' => $payload['vendorTxCode'],
            'amount' => $amountPence,
            'currency' => 'GBP',
            'raw_request' => json_encode($payload),
            'raw_response' => $response->body(),
            'status' => $response->status(),
            'transaction_id' => $response->json('transactionId')
        ]);

        if ($response->status() == 201) {
            $order->update(['status' => 'refunded']);

            $appointment = Appointment::find($order->appointment_id);
            if ($appointment) {
                $appointment->update(['payment_status' => 'refunded']);
            }

            Log::info('Refund successful', [
                'refund_id' => $refund->id,
                'transaction_id' => $refund->transaction_id
            ]);
        }

        return response()->json([
            'status' => $response->status(),
            'body' => $response->json()
        ], $response->status());
    }

    /**
     * Optional: Payment return page
     */
    public function paymentReturn(Request $request)
    {
        Log::info('Payment return', $request->all());
        return view('payment-return', ['query' => $request->all()]);
    }
}
