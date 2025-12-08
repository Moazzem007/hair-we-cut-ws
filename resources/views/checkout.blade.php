<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout - Order {{ $order->id }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body{font-family:sans-serif;padding:20px;max-width:800px;margin:0 auto;background:#f5f5f5;}
    .checkout-container{background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1);}
    .amount{font-size:22px;margin-bottom:25px;font-weight:600;}
    .payment{font-size:18px;margin-bottom:25px;color:#666;}
    #submit-button{padding:12px 30px;background:#007bff;color:#fff;border:none;border-radius:4px;font-size:16px;cursor:pointer;transition:background .3s;}
    #submit-button:hover:not(:disabled){background:#0056b3;}
    #submit-button:disabled{background:#ccc;cursor:not-allowed;}
    .opayo-error{display:none;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px;border-radius:4px;margin-top:20px;}
    .opayo-warning{display:none;background:#fff3cd;color:#856404;border:1px solid #ffeaa7;padding:12px;border-radius:4px;margin-top:20px;}
    pre#debug{background:#f8f9fa;border:1px solid #ddd;padding:10px;font-size:11px;max-height:300px;overflow:auto;margin-top:20px;display:none;}
    #threeds-form{display:none;}
    .redirect-message{text-align:center;padding:20px;}
    .redirect-message p{margin:10px 0;font-size:16px;}
    .spinner{border:3px solid #f3f3f3;border-top:3px solid #007bff;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:20px auto;}
    @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
  </style>
</head>
<body>
<div class="checkout-container">
  <h2>Complete Payment</h2>
  <div class="amount">Order #{{ $order->id }} — £{{ number_format($order->amount / 100, 2) }}</div>
  <div class="payment">Appointment #{{ $appointment->id }}</div>

  <form id="checkout-form" onsubmit="return false;">
    <div id="sp-container"></div>
    <button id="submit-button" type="button" disabled>Loading…</button>
  </form>

  <div id="opayo-errors" class="opayo-error"></div>
  <div id="opayo-warning" class="opayo-warning"></div>
</div>

<!-- 3DS Challenge Form (hidden, auto-submits when 3DS required) -->
<form id="threeds-form" method="POST" action="" target="_self">
  <input type="hidden" name="creq" id="creq-input" value="">
  <input type="hidden" name="threeDSSessionData" id="threeds-session-input" value="">
  <div class="redirect-message">
    <div class="spinner"></div>
    <p><strong>Redirecting to your bank for secure authentication</strong></p>
    <p>Please do not close this window or press the back button.</p>
  </div>
</form>

<pre id="debug"></pre>

<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>

<script>
(async function(){
  const orderId = {{ $order->id }};
  const appointmentId = {{ $appointment->id }};
  let msk = "{{ $merchantSessionKey }}";
  const DEBUG_MODE = {{ config('app.debug') ? 'true' : 'false' }};

  const submitBtn = document.getElementById("submit-button");
  const errorBox = document.getElementById("opayo-errors");
  const warningBox = document.getElementById("opayo-warning");
  const debugEl = document.getElementById("debug");

  if(DEBUG_MODE) debugEl.style.display = "block";

  function debug(...args){
    console.log(...args); 
    if(DEBUG_MODE){
      debugEl.textContent += args.map(x=>typeof x==="object"?JSON.stringify(x,null,2):x).join(" ")+"\n";
    }
  }
  
  function showError(msg){
    errorBox.style.display="block"; 
    errorBox.innerHTML="<b>Error:</b> "+msg;
    window.scrollTo({top:0, behavior:'smooth'});
  }
  
  function showWarning(msg){
    warningBox.style.display="block"; 
    warningBox.innerHTML="<b>Notice:</b> "+msg;
  }

  // Validate MSK exists
  if(!msk || msk.trim() === ""){
    showError("Merchant Session Key not generated. Please refresh the page.");
    debug("CRITICAL: No MSK provided from backend");
    return;
  }

  // Validate sagepay.js loaded
  if(!window.sagepayCheckout){
    showError("Payment system failed to load. Please check your internet connection and refresh.");
    debug("CRITICAL: sagepay.js not loaded");
    return;
  }

  debug("=== INITIALIZATION ===");
  debug("Order ID:", orderId);
  debug("Appointment ID:", appointmentId);
  debug("MSK:", msk);
  debug("MSK Length:", msk.length);

  let checkout = null;
  
  // Initialize Drop-in
  try{
    debug("Initializing Opayo Drop-in...");
    checkout = sagepayCheckout({
      merchantSessionKey: msk,
      onTokenise: onToken,
    });
    
    debug("Mounting form to #sp-container...");
    checkout.form("#sp-container");
    
    submitBtn.disabled = false;
    submitBtn.textContent = "Pay Now";
    debug("✓ Drop-in initialized successfully");
  }catch(e){
    debug("✗ Drop-in initialization failed:", e);
    showError("Payment form failed to initialize. This may be due to an expired session. Please refresh the page.");
    
    // Check if MSK expired (400 seconds)
    if(e.message && e.message.includes("401")){
      showWarning("Your payment session has expired. Please refresh the page to continue.");
    }
    return;
  }

  // Tokenization callback
  async function onToken(result){
    debug("=== TOKENIZATION CALLBACK ===");
    debug("Tokenization result:", result);
    
    if(!result.success){
      const errorMsg = result.error?.errorMessage || "Card tokenization failed";
      const errorCode = result.error?.errorCode;
      
      debug("✗ Tokenization failed");
      debug("Error code:", errorCode);
      debug("Error message:", errorMsg);
      
      // Handle specific error codes
      if(errorCode === 1002){
        showError("Payment session expired. Please refresh the page and try again.");
        showWarning("Tip: Complete payment within 6 minutes of loading this page.");
      } else if(errorCode === 4020){
        showError("Invalid card details. Please check your card number, expiry date, and CVV.");
      } else {
        showError(errorMsg);
      }
      
      submitBtn.disabled = false; 
      submitBtn.textContent = "Pay Now"; 
      return;
    }
    
    debug("✓ Card tokenized successfully");
    debug("Card Identifier:", result.cardIdentifier);
    
    await processPayment(result.cardIdentifier);
  }

  // Process payment
  async function processPayment(cardIdentifier){
    debug("=== PROCESSING PAYMENT ===");
    
    const sessionData = btoa(`order_${orderId}`);
    
    const payload = {
      appointment_id: appointmentId,
      order_id: orderId,
      merchantSessionKey: msk,
      cardIdentifier: cardIdentifier,
      strongCustomerAuthentication: {
        notificationURL: "{{ route('handle3DSNotification') }}", // Must be absolute URL - web route
        browserIP: "{{ request()->ip() }}",
        browserJavaEnabled: navigator.javaEnabled(),
        browserJavascriptEnabled: true,
        browserColorDepth: String(screen.colorDepth),
        browserScreenHeight: String(screen.height),
        browserScreenWidth: String(screen.width),
        browserTZ: String(new Date().getTimezoneOffset()),
        browserUserAgent: navigator.userAgent,
        browserLanguage: navigator.language,
        browserAcceptHeader: "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
      }
    };

    debug("Transaction payload:", payload);

    try{
      debug("Sending transaction request...");
      
      const response = await fetch("{{ url('/api/transactions') }}",{
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();
      debug("Backend response status:", response.status);
      debug("Backend response data:", data);

      // Handle 3DS Authentication Required
      if (data.body && data.body.status === "3DAuth") {
        debug("=== 3DS AUTHENTICATION REQUIRED ===");
        
        if (!data.body.acsUrl || !data.body.cReq) {
          throw new Error("Invalid 3DS data received from gateway");
        }

        debug("ACS URL:", data.body.acsUrl);
        debug("cReq length:", data.body.cReq.length);
        debug("Transaction ID:", data.body.transactionId);
        
        // Store transaction info
        sessionStorage.setItem('opayo_transaction_id', data.body.transactionId);
        sessionStorage.setItem('opayo_order_id', orderId);
        
        // Populate 3DS form
        document.getElementById("threeds-form").action = data.body.acsUrl;
        document.getElementById("creq-input").value = data.body.cReq;
        document.getElementById("threeds-session-input").value = sessionData;
        
        // Show redirect UI
        document.querySelector(".checkout-container").style.display = "none";
        document.getElementById("threeds-form").style.display = "block";
        
        debug("Submitting to ACS for authentication...");
        
        // Auto-submit to bank
        setTimeout(() => {
          document.getElementById("threeds-form").submit();
        }, 500);
        
        return;
      }

      // Handle immediate authorization (no 3DS)
      if(data.body && data.body.status === "Ok"){
        debug("=== PAYMENT AUTHORIZED (NO 3DS) ===");
        debug("Transaction ID:", data.body.transactionId);
        alert("Payment successful!");
        window.location.href = "{{ url('/payment/success') }}?order=" + orderId;
        return;
      }

      // Handle rejected/failed
      if(response.status >= 400 || data.body?.status === "Rejected"){
        const statusDetail = data.body?.statusDetail || "Payment declined";
        debug("✗ Payment rejected:", statusDetail);
        throw new Error(statusDetail);
      }

      // Unexpected response
      debug("✗ Unexpected response format");
      throw new Error("Unexpected response from payment gateway");

    }catch(error){
      debug("=== PAYMENT ERROR ===");
      console.error("Payment error:", error);
      debug("Error message:", error.message);
      
      showError(error.message || "Payment processing failed. Please try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = "Pay Now";
    }
  }

  // Submit button handler
  submitBtn.addEventListener("click", async ()=>{
    debug("=== PAY NOW CLICKED ===");
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing…";
    errorBox.style.display = "none";
    warningBox.style.display = "none";
    
    try{
      debug("Starting tokenization...");
      await checkout.tokenise();
    }catch(error){
      debug("✗ Tokenization error:", error);
      console.error("Tokenization error:", error);
      showError("Unable to process payment. Please check your card details and try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = "Pay Now";
    }
  });

})();
</script>
</body>
</html>