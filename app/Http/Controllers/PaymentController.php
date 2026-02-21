<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\OpayoService;
use App\Models\PaymentOrders as Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FcmController;

class PaymentController extends Controller
{
    protected $opayo;
    public $fcmController;
    public function __construct(OpayoService $opayo)
    {
        $this->opayo = $opayo;
        $this->fcmController = new FcmController();
    }

    // 1) Create Order (API)
    public function createPaymentOrder(Request $r)
    {
        $data = $r->validate(['appointment_id' => 'required|numeric', 'amount' => 'required|numeric', 'reference' => 'nullable|string']);
        // create local order (simplified)
        $order = Order::create([
            'reference' => $data['reference'] ?? 'ORD-' . time(),
            'amount' => intval(round($data['amount'] * 100)),
            'currency' => 'GBP',
            'appointment_id' => $data['appointment_id']
        ]);

        $appointment = Appointment::find($data['appointment_id']);
        $appointment->payment_status = 'pending';
        $appointment->update();

        return response()->json([
            'order_id' => $order->id,
            'checkout_url' => url('/checkout/' . $order->id . '/' . $data['appointment_id'])
        ], 201);
    }

    // 2) Checkout page (webview) — returns Blade with merchantSessionKey
    public function checkoutPage(Order $order, Appointment $appointment)
    {
        // create merchantSessionKey server-side
        $vendorName = config('services.opayo.vendor_name', 'sandbox');
        $customer = Customer::findOrFail($appointment->customer_id);
        $resp = $this->opayo->createMerchantSessionKey($vendorName);
        if ($resp->failed()) {
            // \Log::error('MSK failed', ['status'=>$resp->status(),'body'=>$resp->body()]);
            abort(500, 'Payment provider error');
        }
        $body = $resp->json();
        $merchantSessionKey = $body['merchantSessionKey'] ?? null;
        return view('checkout', compact('order', 'merchantSessionKey', 'appointment', 'customer'));
    }

    // 3) Register transaction: backend receives cardIdentifier from drop-in
    public function registerTransaction(Request $r)
    {
        Log::info('Opayo: Called registerTransaction');

        // Validate incoming request
        $data = $r->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'billing_address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'appointment_id' => 'required|integer|exists:appointments,id',
            'order_id'       => 'required|integer|exists:payment_orders,id',
            'merchantSessionKey' => 'required|string',
            'cardIdentifier'     => 'required|string'
        ]);

        $order = Order::findOrFail($data['order_id']);
        $appointment = Appointment::findOrFail($data['appointment_id']);
        $customer = Customer::findOrFail($appointment->customer_id);

        $customer->update([
            'name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'contact' => $data['contact'],
            'billing_address' => $data['billing_address'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
        ]);

        $customer->save();



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
        $userAgent = request()->header('User-Agent');

        // $callbackUrl = url('/opayo/callback');
        $clientIp = $r->ip();
        $payload = [
            "transactionType" => "Payment",
            "amount" => $amountInPence,
            "paymentMethod" => [
                "card" => [
                    "merchantSessionKey" => $data['merchantSessionKey'],
                    "cardIdentifier" => $data['cardIdentifier'],
                    "reusable" => false,
                    "save" => false,
                ],
                // "paypal" => [
                //     "merchantSessionKey" => $data['merchantSessionKey'],
                //     "callbackUrl" => $callbackUrl,
                // ],
                // "applePay" => [
                //     "clientIpAddress" => $clientIp,
                //     "merchantSessionKey" => $data['merchantSessionKey'],
                //     "sessionValidationToken" => "SFGVHSBEVGAV/VDAYRR+345S",
                //     "paymentData" => "AAAAAAABBBBBCCCCCC",
                //     "applicationData" => "FOeVKLA...PFE4wrw==",
                //     "displayName" => "Visa 1234",
                // ],
                // "googlePay" => [
                //     "payload" => "AAAAAAABBBBBCCCCCC",
                //     "clientIpAddress" => "10.20.30.40",
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                // ],
                // "ideal" => [
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                //     "callbackUrl" => "https://www.example.com",
                //     "languageCode" => "en",
                // ],
                // "alipay" => [
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                //     "callbackUrl" => "https://www.example.com",
                //     "languageCode" => "en",
                //     "shopperPlatform" => "mobile",
                // ],
                // "wechatpay" => [
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                //     "callbackUrl" => "https://www.example.com",
                //     "languageCode" => "en",
                //     "bic" => "SFRTD45",
                // ],
                // "eps" => [
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                //     "callbackUrl" => "https://www.example.com",
                //     "languageCode" => "en",
                //     "bic" => "SFRTD45",
                // ],
                // "trustly" => [
                //     "merchantSessionKey" => "90BDF208-3C19-40AC-858B-3F4054DCD1C0",
                //     "callbackUrl" => "https://www.example.com",
                //     "languageCode" => "en",
                //     "clientIpAddress" => "10.20.30.40",
                //     "beneficiaryId" => "string",
                //     "beneficiaryName" => "string",
                //     "beneficiaryAddress" => "string",
                //     "beneficiaryCountryCode" => "string",
                // ],
            ],
            "vendorTxCode" => $vendorTxCode,
            "currency" => "GBP",
            "description" => "Transaction",
            "customerFirstName" => $customer->name ?? "Customer",
            "customerLastName" => $customer->last_name ?? "Name",
            "billingAddress" => [
                "address1" => $customer->billing_address,
                "city" => $customer->city ?? "N/A",
                "postalCode" => $customer->postal_code,
                "country" => "GB"
            ],
            "customerEmail"     => $customer->email ?? "unknown@example.com",
            "customerPhone"     => $customer->contact ?? null,
            // "settlementReferenceText" => "123456GRTY234",
            // "entryMethod" => "Ecommerce",
            // "giftAid" => false,
            "apply3DSecure" => "Force",
            "applyAvsCvcCheck" => "Disable",
            // "shippingDetails" => [
            //     "recipientFirstName" => "Sam",
            //     "recipientLastName" => "Jones",
            //     "shippingAddress1" => "407 St. John Street",
            //     "shippingCity" => "London",
            //     "shippingCountry" => "GB",
            //     "shippingAddress2" => "string",
            //     "shippingAddress3" => "string",
            //     "shippingPostalCode" => "EC1V 4AB",
            //     "shippingState" => "st",
            // ],
            // "referrerId" => "f9979593-a390-4069-b126-7914783fc",
            "strongCustomerAuthentication" => [
                "notificationURL" => route('handle3DSNotification', [], true),
                "browserIP" => $clientIp,
                "browserAcceptHeader" => "*/*",
                "browserJavascriptEnabled" => false,
                "browserUserAgent" => $userAgent,
                "challengeWindowSize" => "Small",
                "transType" => "GoodsAndServicePurchase",
                "browserLanguage" => "en-US",
                "browserJavaEnabled" => true,
                "browserColorDepth" => "48",
                "browserScreenHeight" => "700",
                "browserScreenWidth" => "500",
                // "browserTZ" => "st",
                // "acctID" => "string",
                "threeDSRequestorAuthenticationInfo" => [
                    "threeDSReqAuthData" => "User authenticated using 2FA (email + TOTP). Session ID: abc123xyz",
                    "threeDSReqAuthMethod" => "LoginWithThreeDSRequestorCredentials",
                    "threeDSReqAuthTimestamp" => Carbon::now('UTC')->format('YmdHi'),
                ],
                // might be required
                // "threeDSRequestorPriorAuthenticationInfo" => [
                //     "threeDSReqPriorAuthData" => "data",
                //     "threeDSReqPriorAuthMethod" => "FrictionlessAuthentication",
                //     "threeDSReqPriorAuthTimestamp" => Carbon::now('UTC')->format('YmdHi'),
                //     "threeDSReqPriorRef" => "2cd842f5-da5d-40b7-8ae6-6ce61cc7b580",
                // ],
                "acctInfo" => [
                    "chAccAgeInd" => "GuestCheckout",
                    "chAccChange" => Carbon::now('UTC')->format('Ymd'),
                    "chAccChangeInd" => "MoreThanSixtyDays",
                    "chAccDate" => Carbon::now('UTC')->format('Ymd'),
                    "chAccPwChange" => Carbon::now('UTC')->format('Ymd'),
                    "chAccPwChangeInd" => "MoreThanSixtyDays",
                    "nbPurchaseAccount" => "5",
                    "provisionAttemptsDay" => "0",
                    "txnActivityDay" => "0",
                    "txnActivityYear" => "5",
                    "paymentAccAge" => Carbon::now('UTC')->format('Ymd'),
                    "paymentAccInd" => "GuestCheckout",
                    "shipAddressUsage" => Carbon::now('UTC')->format('Ymd'),
                    "shipAddressUsageInd" => "MoreThanSixtyDays",
                    "shipNameIndicator" => "FullMatch",
                    "suspiciousAccActivity" => "NotSuspicious"
                ],
                // "merchantRiskIndicator" => [
                //     "deliveryEmailAddress" => "customer@domain.com",
                //     "deliveryTimeframe" => "OvernightShipping",
                //     "giftCardAmount" => "123",
                //     "giftCardCount" => "2",
                //     "preOrderDate" => "20200220",
                //     "preOrderPurchaseInd" => "MerchandiseAvailable",
                //     "reorderItemsInd" => "Reordered",
                //     "shipIndicator" => "CardholderBillingAddress",
                // ],
                "threeDSExemptionIndicator" => "TransactionRiskAnalysis",
                "website" => "https://hairwecut.co.uk",
            ],
            "customerMobilePhone" => $customer->contact,
            // "customerWorkPhone"=> "+441234567891",
            "credentialType" => [
                "cofUsage" => "First",
                "initiatedType" => "CIT",
                "mitType" => "Unscheduled",
                // "recurringExpiry" => "20200301",
                // "recurringFrequency" => "28",
                // "purchaseInstalData" => "6",
            ],
            "fiRecipient" => [
                "accountNumber" => "1234567890",
                "surname" => "Surname",
                "postcode" => "EC1V 8AB",
                "dateOfBirth" => "19900101",
            ],
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
            // $appointment->update(['payment_status' => 'paid']);
            $payment->requires_3ds = false;
        } elseif (isset($body['3DSecure']) && $body['3DSecure']['status'] === 'NotChecked') {
            $payment->requires_3ds = true;
            $payment->three_ds_data = $body;
        } else {
            $order->update(['status' => 'payment_failed']);
            // $appointment->update(['payment_status' => 'failed']);
        }

        // $appointment->save();
        $payment->save();

        return response()->json([
            'status' => $resp->status(),
            'body'   => $body
        ], $resp->status());
    }

    /**
     * Handle 3DS notification callback from the bank
     */
    public function handle3DSNotification(Request $request)
    {
        // Log raw input for debugging
        Log::info('Opayo: 3DS Notification - Raw Input', [
            'all' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        // Validate input from bank
        $validated = $request->validate([
            'cres' => 'required|string',  // Lowercase 'cres' from ACS
            'threeDSSessionData' => 'required|string'
        ]);

        // Decode session data
        $sessionData = base64_decode($validated['threeDSSessionData']);
        Log::info('Opayo: Session data decoded', ['sessionData' => $sessionData]);

        // Extract order ID (format: "order_123")
        preg_match('/order_(\d+)/', $sessionData, $matches);
        $orderId = $matches[1] ?? null;

        if (!$orderId) {
            Log::error('Opayo: Invalid session data format', [
                'sessionData' => $sessionData,
                'expected_format' => 'order_{id}'
            ]);
            return $this->redirectToFailure('Invalid session data');
        }

        // Find payment record by order_id
        $payment = Payment::where('order_id', $orderId)
            ->whereNotNull('transaction_id')
            ->latest()
            ->first();

        if (!$payment) {
            Log::error('Opayo: Payment record not found', [
                'orderId' => $orderId
            ]);
            return $this->redirectToFailure('Payment record not found');
        }

        Log::info('Opayo: Submitting cRes to gateway', [
            'transactionId' => $payment->transaction_id,
            'orderId' => $orderId,
            'cres_length' => strlen($validated['cres'])
        ]);

        // Submit cRes to Opayo
        $response = $this->submit3DSecureChallenge(
            $payment->transaction_id,
            $validated['cres']
        );

        if (!$response) {
            Log::error('Opayo: Failed to submit 3DS challenge');
            return $this->redirectToFailure('Unable to complete authentication');
        }

        $body = $response->json();

        Log::info('Opayo: 3DS Challenge response received', [
            'status' => $body['status'] ?? 'unknown',
            'statusCode' => $body['statusCode'] ?? 'unknown',
            'statusDetail' => $body['statusDetail'] ?? ''
        ]);

        // Update payment record
        $payment->raw_response = ($payment->raw_response ?? '') . "\n3DS Challenge: " . json_encode($body);

        // Check authentication result
        if (isset($body['status']) && $body['status'] === 'Ok') {
            // Success!
            $payment->status = 'completed';
            $payment->save();

            // Update order and appointment
            $order = $payment->order;
            $order->update(['status' => 'paid']);

            if ($order->appointment) {
                $order->appointment->update(['payment_status' => 'paid']);
            }

            Log::info('Opayo: Payment successful', [
                'orderId' => $orderId,
                'transactionId' => $payment->transaction_id,
                'amount' => $order->amount
            ]);

            return $this->redirectToSuccess($orderId);
        } else {
            // Failed authentication
            $payment->status = '3ds_failed';
            $payment->save();

            $statusDetail = $body['statusDetail'] ?? 'Authentication failed';

            Log::warning('Opayo: 3DS authentication failed', [
                'orderId' => $orderId,
                'status' => $body['status'] ?? 'unknown',
                'statusDetail' => $statusDetail
            ]);

            return $this->redirectToFailure($statusDetail);
        }
    }

    /**
     * Submit 3DS challenge response to Opayo
     */
    private function submit3DSecureChallenge($transactionId, $cRes)
    {
        $integrationKey = config('services.opayo.integration_key');
        $integrationPassword = config('services.opayo.integration_password');
        $endpoint = config('services.opayo.endpoint');

        $authString = base64_encode("{$integrationKey}:{$integrationPassword}");

        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$authString}",
                'Content-Type' => 'application/json',
            ])->post("{$endpoint}/api/v1/transactions/{$transactionId}/3d-secure-challenge", [
                'cRes' => $cRes  // IMPORTANT: Capital R
            ]);

            Log::info('Opayo: 3DS challenge API response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Opayo: 3DS challenge submission exception', [
                'transactionId' => $transactionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Redirect to success page with HTML
     */
    private function redirectToSuccess($orderId)
    {
        $order = Order::find($orderId);
        $appointment = Appointment::find($order->appointment_id);
        $appointment->update(['payment_status' => 'paid']);

        $url = url("/payment/success?order={$orderId}");

        return response()->make("<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Payment Successful</title>
    <meta http-equiv='refresh' content='1;url={$url}'>
    <style>
        body{font-family:sans-serif;text-align:center;padding:50px;background:#f0f9f0;}
        .success{color:#28a745;font-size:24px;margin:20px 0;}
        .spinner{border:4px solid #f3f3f3;border-top:4px solid #28a745;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:30px auto;}
        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div class='success'>✓ Payment Successful!</div>
    <div class='spinner'></div>
    <p>Redirecting you back to your account...</p>
    <script>
        setTimeout(function(){ window.location.href = '{$url}'; }, 1000);
    </script>
</body>
</html>", 200)->header('Content-Type', 'text/html');
    }

    /**
     * Redirect to failure page with HTML
     */
    private function redirectToFailure($message = 'Payment failed')
    {
        $url = url("/payment/failed?error=" . urlencode($message));

        return response()->make("<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Payment Failed</title>
    <meta http-equiv='refresh' content='3;url={$url}'>
    <style>
        body{font-family:sans-serif;text-align:center;padding:50px;background:#fff5f5;}
        .error{color:#dc3545;font-size:24px;margin:20px 0;}
        .message{color:#666;font-size:16px;margin:20px 0;}
    </style>
</head>
<body>
    <div class='error'>✗ Payment Failed</div>
    <div class='message'>" . htmlspecialchars($message) . "</div>
    <p>Redirecting you back...</p>
    <script>
        setTimeout(function(){ window.location.href = '{$url}'; }, 3000);
    </script>
</body>
</html>", 200)->header('Content-Type', 'text/html');
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order');

        if (!$orderId) {
            return redirect('/')->with('error', 'Invalid payment confirmation');
        }

        $order = Order::find($orderId);

        $appointment = Appointment::find($order->appointment_id);
        
        $maxInv = Wallet::max('inv');

        $nextInv = ($maxInv ?? 0) + 1;

        $debitAmount = (floatval($order->amount) / 100) * 0.8;

        $barber = Barber::find($appointment->barber_id);

        $user = null;
        if (!empty($barber->barber_of)) {
            $user = Barber::find($barber->barber_of);
        }

        if ($user && !empty($user->device_token)) {
            $this->fcmController->sendNotification(new \Illuminate\Http\Request([
                'token' => $user->device_token,
                'title' => 'New Appointment',
                'body' => 'You have a new appointment request.',
                'email' => $user->email,
            ]));
        }

        Wallet::create([
            'user_id'        => $appointment->customer_id,
            'barber_id'      => $appointment->barber_id,
            'salon_id'       => $appointment->salon_id,
            'appointment_id' => $appointment->id,
            'inv'            => $nextInv,
            'debit'          => $debitAmount,
            'credit'         => 0,
            'com_amount'     => 0,
            'description'    => 'Appointment Booking Payment',
        ]);

        if (!$order) {
            return redirect('/')->with('error', 'Order not found');
        }

        return view('payment.success', [
            'order' => $order
        ]);
    }

    /**
     * Payment failed page
     */
    public function paymentFailed(Request $request)
    {
        $errorMessage = $request->query('error', 'Payment processing failed');

        return view('payment.failed', [
            'error' => $errorMessage
        ]);
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
        $this->validate($r, ['amount' => 'required|numeric']);
        $amountPence = intval(round($r->amount * 100));

        // find last successful transaction id
        $last = $order->payments()->whereNotNull('transaction_id')->latest()->first();
        if (!$last) return response()->json(['error' => 'No transaction to refund'], 422);

        $payload = [
            'transactionType' => 'Refund',
            'relatedTransactionId' => $last->transaction_id,
            'vendorTxCode' => 'refund-' . $order->id . '-' . uniqid(),
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

        return response()->json(['status' => $resp->status(), 'body' => $resp->json()], $resp->status());
    }

    // Optional return view after payment
    public function paymentReturn(Request $r)
    {
        return view('payment-return', ['query' => $r->all()]);
    }

    public function handleCallback(Request $request)
    {
        // 1. Verify the payload / signature if provided
        // 2. Update your order/payment status
        // 3. Return a 200 OK response
        return response()->json(['status' => 'success']);
    }
}
