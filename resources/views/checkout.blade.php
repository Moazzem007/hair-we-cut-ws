<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout - Order {{ $order->id }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }
    .checkout-container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    h2 {
      color: #333;
      margin-bottom: 10px;
      font-size: 28px;
    }
    .order-info {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin: 20px 0;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      color: #666;
    }
    .info-row:last-child {
      margin-bottom: 0;
      padding-top: 10px;
      border-top: 2px solid #ddd;
      font-weight: bold;
      font-size: 20px;
      color: #333;
    }
    .info-label { font-weight: 500; }
    .info-value { font-weight: 600; color: #333; }

    #sp-container {
      margin: 30px 0;
      min-height: 120px;
    }

    #submit-button {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }
    #submit-button:hover:not(:disabled) {
      transform: translateY(-2px);
    }
    #submit-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .opayo-error {
      display: none;
      background: #fee;
      color: #c33;
      border: 1px solid #fcc;
      padding: 15px;
      border-radius: 8px;
      margin-top: 20px;
    }
    .opayo-error.show {
      display: block;
    }

    .loading {
      text-align: center;
      padding: 20px;
      color: #666;
    }
    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #667eea;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 10px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    #debug {
      background: #f8f9fa;
      border: 1px solid #ddd;
      padding: 15px;
      font-size: 11px;
      max-height: 300px;
      overflow: auto;
      margin-top: 20px;
      border-radius: 8px;
      font-family: 'Courier New', monospace;
      white-space: pre-wrap;
      word-wrap: break-word;
    }

    .secure-badge {
      text-align: center;
      margin-top: 20px;
      color: #999;
      font-size: 12px;
    }
  </style>
</head>

<body>

<div class="checkout-container">
  <h2>Complete Payment</h2>

  <div class="order-info">
    <div class="info-row">
      <span class="info-label">Order Reference:</span>
      <span class="info-value">{{ $order->reference }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Appointment ID:</span>
      <span class="info-value">#{{ $appointment->id }}</span>
    </div>
    <div class="info-row">
      <span class="info-label">Total Amount:</span>
      <span class="info-value">£{{ number_format($order->amount / 100, 2) }}</span>
    </div>
  </div>

  <form id="checkout-form">
    <div id="sp-container"></div>
    <button id="submit-button" type="button" disabled>
      <div class="loading">
        <div class="spinner"></div>
        Initializing...
      </div>
    </button>
  </form>

  <div id="opayo-errors" class="opayo-error"></div>

  <div class="secure-badge">
    🔒 Secure payment powered by Opayo
  </div>
</div>

<pre id="debug"></pre>

<!-- Opayo/SagePay Script -->
<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>

<script>
(function () {
  'use strict';

  // Configuration
  const orderId = {{ $order->id }};
  const appointmentId = {{ $appointment->id }};
  const orderAmount = {{ $order->amount }};
  const msk = "{{ $merchantSessionKey }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // DOM Elements
  const submitBtn = document.getElementById("submit-button");
  const errorBox = document.getElementById("opayo-errors");
  const debugEl = document.getElementById("debug");

  // State
  let checkout = null;
  let isProcessing = false;

  // Debug function
  function debug(...args) {
    const timestamp = new Date().toLocaleTimeString();
    const message = `[${timestamp}] ${args.map(x =>
      typeof x === 'object' ? JSON.stringify(x, null, 2) : x
    ).join(' ')}`;

    console.log(...args);
    debugEl.textContent += message + '\n\n';
    debugEl.scrollTop = debugEl.scrollHeight;
  }

  // Error handling
  function showError(msg) {
    errorBox.innerHTML = `<strong>Error:</strong> ${msg}`;
    errorBox.classList.add('show');
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Try Again';
    debug('ERROR:', msg);
  }

  function hideError() {
    errorBox.classList.remove('show');
  }

  // Validation checks
  debug('=== INITIALIZATION ===');
  debug('Order ID:', orderId);
  debug('Appointment ID:', appointmentId);
  debug('Amount:', orderAmount);
  debug('MSK:', msk ? 'Present (' + msk.substring(0, 20) + '...)' : 'MISSING');
  debug('CSRF Token:', csrfToken ? 'Present' : 'MISSING');

  if (!window.sagepayCheckout) {
    showError("Payment system failed to load. Please refresh the page.");
    debug("ERROR: sagepayCheckout not found on window");
    return;
  }

  if (!msk) {
    showError("Payment configuration error. Please try again.");
    debug("ERROR: Missing Merchant Session Key");
    return;
  }

  if (!csrfToken) {
    showError("Security token missing. Please refresh the page.");
    debug("ERROR: Missing CSRF Token");
    return;
  }

  // Initialize Opayo Drop-in
  debug('=== MOUNTING DROP-IN ===');

  try {
    checkout = sagepayCheckout({
      merchantSessionKey: msk,
      onTokenise: handleTokenise
    });

    // Mount the form
    checkout.form("#sp-container");

    debug('Drop-in mounted successfully');

    // Enable submit button
    submitBtn.disabled = false;
    submitBtn.innerHTML = `Pay £${(orderAmount / 100).toFixed(2)}`;

  } catch (error) {
    debug('Mount error:', error);
    showError("Failed to initialize payment form. Please refresh the page.");
    return;
  }

  // Handle tokenisation callback
  function handleTokenise(result) {
    debug('=== TOKENISE CALLBACK ===');
    debug('Result:', result);

    if (!result.success) {
      const errorMsg = result.error?.errorMessage || 'Card validation failed';
      showError(errorMsg);
      isProcessing = false;
      return;
    }

    // Check if 3DS is required
    if (result.requires3DS) {
      debug('3DS authentication required - Drop-In will handle');
      // The Drop-In will automatically show the 3DS challenge
      // and call this callback again after completion
      return;
    }

    // Card tokenised successfully - process payment
    debug('Card tokenised successfully');
    debug('Card Identifier:', result.cardIdentifier);

    processPayment(result.cardIdentifier);
  }

  // Process payment with backend
  async function processPayment(cardIdentifier) {
    debug('=== PROCESSING PAYMENT ===');

    hideError();
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Processing...';

    const payload = {
      appointment_id: appointmentId,
      order_id: orderId,
      merchantSessionKey: msk,
      cardIdentifier: cardIdentifier
    };

    debug('Payload:', payload);
    debug('URL: /api/transactions');

    try {
      const response = await fetch('/api/transactions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });

      debug('Response Status:', response.status);
      debug('Response OK:', response.ok);

      // Get response text first
      const responseText = await response.text();
      debug('Response Text (first 500 chars):', responseText.substring(0, 500));

      // Try to parse JSON
      let data;
      try {
        data = JSON.parse(responseText);
        debug('Parsed Response:', data);
      } catch (parseError) {
        debug('JSON Parse Error:', parseError.message);
        throw new Error('Server returned invalid response. Please try again.');
      }

      // Check for errors
      if (!response.ok) {
        const errorMsg = data.body?.errors?.[0]?.description ||
                        data.message ||
                        `Server error (${response.status})`;
        throw new Error(errorMsg);
      }

      // Check response status from Opayo
      const opayoStatus = data.body?.status || data.status;
      debug('Opayo Status:', opayoStatus);

      // Handle 3DS requirement from backend
      if (response.status === 202 || data.body?.acsUrl) {
        debug('3DS required from backend');
        debug('3DS Data:', data.body);

        // Handle 3DS redirect
        handle3DSecure(data.body);
        return;
      }

      // Handle successful payment
      if (response.status === 201 || opayoStatus === 'Ok') {
        debug('=== PAYMENT SUCCESSFUL ===');
        debug('Transaction ID:', data.body?.transactionId);

        // Redirect to success
        const successUrl = `myapp://payment-success?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&transaction_id=${encodeURIComponent(data.body?.transactionId || '')}`;

        debug('Redirecting to:', successUrl);
        window.location.href = successUrl;
        return;
      }

      // Payment declined or other error
      const statusDetail = data.body?.statusDetail || 'Payment was declined';
      throw new Error(statusDetail);

    } catch (error) {
      debug('=== PAYMENT FAILED ===');
      debug('Error:', error.message);
      debug('Stack:', error.stack);

      // Show error to user
      showError(error.message || 'Payment failed. Please try again.');

      // Redirect to failure
      const failureUrl = `myapp://payment-failed?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&error=${encodeURIComponent(error.message)}`;

      setTimeout(() => {
        debug('Redirecting to:', failureUrl);
        window.location.href = failureUrl;
      }, 3000); // Wait 3 seconds so user can see the error
    }
  }

  // Handle 3D Secure
  function handle3DSecure(threeDSData) {
    debug('=== HANDLING 3D SECURE ===');
    debug('ACS URL:', threeDSData.acsUrl);
    debug('cReq present:', !!threeDSData.cReq);

    if (!threeDSData.acsUrl || !threeDSData.cReq) {
      showError('3D Secure data missing. Please try again.');
      return;
    }

    // Create form and submit to ACS
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = threeDSData.acsUrl;

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'creq'; // lowercase 'creq' is important
    input.value = threeDSData.cReq;

    form.appendChild(input);
    document.body.appendChild(form);

    debug('Submitting 3DS form to ACS...');
    form.submit();
  }

  // Submit button click handler
  submitBtn.addEventListener('click', async () => {
    if (isProcessing) {
      debug('Already processing, ignoring click');
      return;
    }

    debug('=== SUBMIT BUTTON CLICKED ===');
    isProcessing = true;
    hideError();

    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Processing...';

    try {
      // Trigger tokenisation
      debug('Calling checkout.tokenise()...');
      await checkout.tokenise();

      // The onTokenise callback will be called with the result
      // and will handle the rest of the flow

    } catch (error) {
      debug('Tokenise error:', error);
      showError('Failed to process card details. Please check your card information.');
      isProcessing = false;
      submitBtn.disabled = false;
      submitBtn.innerHTML = `Pay £${(orderAmount / 100).toFixed(2)}`;
    }
  });

  debug('=== READY ===');
  debug('Click "Pay" button to start payment');

})();
</script>

</body>
</html>
