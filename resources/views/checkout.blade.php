<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout - Order {{ $order->id }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body {
      font-family: system-ui, sans-serif;
      padding: 20px;
      max-width: 800px;
      margin: 0 auto;
      background: #f5f5f5;
    }
    .checkout-container {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #333;
      margin-bottom: 10px;
    }
    .amount {
      font-size: 24px;
      color: #007bff;
      font-weight: bold;
      margin-bottom: 30px;
    }
    #checkout-form {
      margin: 20px 0;
    }
    #sp-container {
      min-height: 300px;
      margin-bottom: 20px;
    }
    #submit-button {
      padding: 12px 30px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }
    #submit-button:hover {
      background: #0056b3;
    }
    #submit-button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }
    pre#opayo-debug {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      padding: 15px;
      margin-top: 20px;
      white-space: pre-wrap;
      border-radius: 4px;
      font-size: 12px;
      max-height: 400px;
      overflow-y: auto;
      font-family: monospace;
    }
    .opayo-error {
      color: #721c24;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
    }
    .loading {
      text-align: center;
      padding: 40px 20px;
      color: #666;
      font-size: 16px;
    }
    .loading::after {
      content: '...';
      animation: dots 1.5s steps(4, end) infinite;
    }
    @keyframes dots {
      0%, 20% { content: '.'; }
      40% { content: '..'; }
      60%, 100% { content: '...'; }
    }
    .retry-button {
      margin-top: 20px;
      padding: 12px 24px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .retry-button:hover {
      background: #0056b3;
    }
    .toggle-debug {
      margin-top: 20px;
      padding: 8px 16px;
      background: #f0f0f0;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
    }
    .toggle-debug:hover {
      background: #e0e0e0;
    }
    .success {
      color: #155724;
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
    }
  </style>
</head>
<body>
  <div class="checkout-container">
    <h2>Complete Your Payment</h2>
    <div class="amount">Order #{{ $order->id }} - £{{ number_format($order->amount / 100, 2) }}</div>

    <!-- Payment Form -->
    <form id="checkout-form">
      <div id="sp-container">
        {{-- <div class="loading">Loading secure payment form</div> --}}
      </div>
      <button type="submit" id="submit-button" disabled>Pay Now</button>
    </form>

    <div id="opayo-errors" class="opayo-error" style="display: none;"></div>
  </div>

  <pre id="opayo-debug" style="display: none;"></pre>

  <!-- CORRECT Sagepay/Opayo Script -->
  <script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>
  <!-- For LIVE: <script src="https://pi-live.sagepay.com/api/v1/js/sagepay.js"></script> -->

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const debugEl = document.getElementById('opayo-debug');
    const errorEl = document.getElementById('opayo-errors');
    const container = document.getElementById('sp-container');
    const submitButton = document.getElementById('submit-button');
    const checkoutForm = document.getElementById('checkout-form');

    // Handle form submission
    checkoutForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Check if the form is valid and ready for submission
      if (submitButton.disabled) {
        debug('Form submission blocked - submit button is disabled');
        return false;
      }

      debug('Form submission intercepted, triggering Opayo tokenization');

      // The actual form submission will be handled by the Opayo integration
      return true;
    });

    // Debug toggle button
    const toggleDebug = document.createElement('button');
    toggleDebug.textContent = 'Show Debug Log';
    toggleDebug.className = 'toggle-debug';
    toggleDebug.addEventListener('click', () => {
      const isVisible = debugEl.style.display !== 'none';
      debugEl.style.display = isVisible ? 'none' : 'block';
      toggleDebug.textContent = isVisible ? 'Show Debug Log' : 'Hide Debug Log';
    });
    document.body.appendChild(toggleDebug);

    function debug(...args) {
      console.log(...args);
      if (debugEl) {
        const message = args.map(arg => {
          try {
            return typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg);
          } catch(e) {
            return String(arg);
          }
        }).join(' ');

        const timestamp = new Date().toISOString().substr(11, 12);
        debugEl.textContent += `[${timestamp}] ${message}\n`;
        debugEl.scrollTop = debugEl.scrollHeight;
      }
    }

    function showUserError(message, details = '') {
      console.error('Payment Error:', message, details);
      if (errorEl) {
        errorEl.innerHTML = `<strong>Payment Error:</strong><br>${message}${details ? '<br>' + details : ''}`;
        errorEl.style.display = 'block';
      }
      debug('ERROR:', message, details);
    }

    function hideError() {
      if (errorEl) {
        errorEl.style.display = 'none';
      }
    }

    const merchantSessionKey = "{{ $merchantSessionKey ?? '' }}";
    debug('=== Opayo Drop-In Initialization ===');
    debug('Merchant Session Key:', merchantSessionKey ? 'Present (masked): ' + merchantSessionKey.substring(0, 8) + '...' : 'MISSING');

    if (!merchantSessionKey) {
      showUserError('Payment initialization failed', 'Missing merchant session key. Please refresh the page.');
      return;
    }

    // Check if sagepayCheckout is loaded
    if (typeof window.sagepayCheckout !== 'function') {
      showUserError('Payment system not available', 'The Sagepay script failed to load. Please refresh the page.');
      debug('Error: sagepayCheckout function not found');
      return;
    }

    debug('Sagepay Drop-In script loaded successfully');

    // Initialize the Drop-In
    function initializePayment() {
      hideError();

      if (!container) {
        showUserError('Payment container not found');
        return;
      }

      // Clear container
    //   container.innerHTML = '<div class="loading">Loading payment form</div>';
      submitButton.disabled = true;
      submitButton.textContent = 'Processing...';

      debug('Calling sagepayCheckout()...');

      try {
        // Initialize Opayo/Sagepay checkout
        const checkout = sagepayCheckout({
          merchantSessionKey: merchantSessionKey
        }).form({
          formSelector: '#checkout-form',  // This is REQUIRED!
          onFormSubmit: function() {
            debug('Opayo form submission started');
            // This function will be called when the form is ready to be submitted
            // The actual submission is handled by Opayo
          },
          onTokenised: function(result) {
            debug('=== Tokenisation Result ===', result);
            alert('hello');

            if (!result || result.success !== true) {
              const errorMsg = result?.error?.message || result?.errorMessage || 'Card validation failed. Please check your details.';
              showUserError(errorMsg);
              debug('Tokenisation failed:', result);
              submitButton.disabled = false;
              submitButton.textContent = 'Pay Now';
              return false;
            }

            // Show processing state
            submitButton.disabled = true;
            submitButton.textContent = 'Processing Payment...';
            container.innerHTML = '<div class="loading">Processing your payment securely</div>';

            debug('Card tokenised successfully. CardIdentifier:', result.cardIdentifier);

            // Prepare transaction data
            const transactionData = {
              order_id: {{ $order->id }},
              merchantSessionKey: merchantSessionKey,
              cardIdentifier: result.cardIdentifier
            };

            debug('Submitting transaction to backend...', transactionData);

            // Submit to backend

            debug('Sending request to /api/transactions with data:', transactionData);

fetch('/api/transactions', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  },
  body: JSON.stringify(transactionData)
})
.then(response => {
  debug('Response status:', response.status, response.statusText);
  debug('Response headers:', Object.fromEntries(response.headers.entries()));

  if (!response.ok) {
    return response.text().then(text => {
      debug('Error response text:', text);
      try {
        const data = JSON.parse(text);
        throw new Error(data.message || `Server error: ${response.status} ${response.statusText}`);
      } catch (e) {
        throw new Error(`Server error: ${response.status} ${response.statusText}. Response: ${text}`);
      }
    });
  }
  return response.json().catch(e => {
    debug('Error parsing JSON response:', e);
    throw new Error('Invalid JSON response from server');
  });
})
.then(data => {
  debug('=== Transaction Success ===', data);
  container.innerHTML = '<div class="success">✓ Payment successful! Redirecting...</div>';
  submitButton.textContent = 'Payment Complete';
  setTimeout(() => {
    window.location.href = "{{ url('/payment-return') }}?order_id={{ $order->id }}&status=success";
  }, 1500);
})
.catch(error => {
  debug('=== Transaction Error ===', error);
  container.innerHTML = `
    <div class="opayo-error">
      <strong>Payment Failed:</strong><br>
      ${error.message || 'An error occurred while processing your payment.'}
      <button onclick="window.retryPayment()" class="retry-button">Try Again</button>
    </div>
  `;
  submitButton.disabled = false;
  submitButton.textContent = 'Try Again';
});

            return false; // Prevent default form submission
          }
        });

        debug('Drop-In form initialized and mounted to #sp-container');

        // Enable submit button once form is loaded
        submitButton.disabled = false;
        submitButton.textContent = 'Pay Now';

        // Store instance
        window.__opayoDropInInstance = checkout;

      } catch (error) {
        console.error('Drop-In initialization error:', error);
        debug('=== Initialization Error ===', error);

        showUserError(
          'Failed to initialize payment form',
          error.message || 'Please refresh the page and try again.'
        );

        container.innerHTML = `
          <div style="text-align: center; padding: 40px 20px;">
            <p style="color: #666; margin-bottom: 20px;">Failed to load payment form</p>
            <button class="retry-button" onclick="window.retryPayment()">Retry</button>
          </div>
        `;
      }
    }

    // Start initialization
    debug('Starting payment initialization...');
    initializePayment();

    // Expose retry function
    window.retryPayment = function() {
      debug('=== Manual Retry Triggered ===');

      // Clean up existing instance
      try {
        if (window.__opayoDropInInstance && typeof window.__opayoDropInInstance.destroy === 'function') {
          window.__opayoDropInInstance.destroy();
          debug('Cleaned up previous Drop-In instance');
        }
      } catch (e) {
        debug('Cleanup error (non-fatal):', e.message);
      }

      window.__opayoDropInInstance = null;
      initializePayment();
    };

    debug('Payment page ready. Use window.retryPayment() to retry manually.');
  });
  </script>
</body>
</html>
