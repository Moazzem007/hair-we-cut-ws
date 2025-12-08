<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout - Order {{ $order->id }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .page-wrapper {
      max-width: 1200px;
      margin: 0 auto;
    }

    .page-header {
      text-align: center;
      color: #fff;
      margin-bottom: 30px;
    }

    .page-header h1 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .page-header p {
      font-size: 16px;
      opacity: 0.9;
    }

    .checkout-container {
      /* Wrapper for the entire checkout section */
    }

    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
      margin-bottom: 25px;
    }

    .checkout-card {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .card-header {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }

    .card-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: #fff;
      font-size: 20px;
    }

    .card-header h2 {
      font-size: 20px;
      font-weight: 600;
      color: #2d3748;
    }

    .order-summary {
      margin-bottom: 20px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .summary-row:last-child {
      border-bottom: none;
      padding-top: 15px;
      margin-top: 10px;
      border-top: 2px solid #667eea;
    }

    .summary-label {
      font-size: 14px;
      color: #718096;
    }

    .summary-value {
      font-size: 14px;
      font-weight: 600;
      color: #2d3748;
    }

    .summary-row:last-child .summary-label {
      font-size: 16px;
      font-weight: 600;
      color: #2d3748;
    }

    .summary-row:last-child .summary-value {
      font-size: 24px;
      color: #667eea;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-label {
      font-size: 13px;
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 6px;
      display: block;
    }

    .form-label .required {
      color: #e53e3e;
      margin-left: 2px;
    }

    .form-input {
      width: 100%;
      padding: 12px 14px;
      font-size: 15px;
      color: #2d3748;
      background: #fff;
      border: 2px solid #e2e8f0;
      border-radius: 6px;
      transition: all 0.3s;
      font-family: inherit;
    }

    .form-input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input::placeholder {
      color: #a0aec0;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    #sp-container {
      margin-bottom: 25px;
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
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    #submit-button:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    #submit-button:disabled {
      background: #cbd5e0;
      cursor: not-allowed;
      box-shadow: none;
    }

    .opayo-error {
      display: none;
      background: #fff5f5;
      color: #c53030;
      border: 1px solid #feb2b2;
      padding: 16px;
      border-radius: 8px;
      margin-top: 20px;
      font-size: 14px;
    }

    .opayo-warning {
      display: none;
      background: #fffbeb;
      color: #92400e;
      border: 1px solid #fde68a;
      padding: 16px;
      border-radius: 8px;
      margin-top: 20px;
      font-size: 14px;
    }

    .security-badge {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 20px;
      padding: 12px;
      background: #f7fafc;
      border-radius: 6px;
      font-size: 13px;
      color: #718096;
    }

    .security-badge::before {
      content: "🔒";
      font-size: 16px;
    }

    pre#debug {
      background: #1a202c;
      color: #e2e8f0;
      border: 1px solid #2d3748;
      padding: 15px;
      font-size: 11px;
      max-height: 300px;
      overflow: auto;
      margin-top: 20px;
      display: none;
      border-radius: 8px;
      font-family: 'Courier New', monospace;
    }

    #threeds-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    #threeds-form {
      display: none;
    }

    .redirect-message {
      text-align: center;
      padding: 40px 20px;
      background: #fff;
      border-radius: 12px;
      max-width: 500px;
      margin: 50px auto;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .redirect-message p {
      margin: 15px 0;
      font-size: 16px;
      color: #2d3748;
    }

    .redirect-message p:first-of-type {
      font-weight: 600;
      font-size: 18px;
    }

    .spinner {
      border: 4px solid #f3f4f6;
      border-top: 4px solid #667eea;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      body {
        padding: 15px;
      }

      .page-header h1 {
        font-size: 24px;
      }

      .page-header p {
        font-size: 14px;
      }

      .checkout-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .checkout-card {
        padding: 20px;
      }

      .card-header h2 {
        font-size: 18px;
      }

      .card-icon {
        width: 35px;
        height: 35px;
        font-size: 18px;
      }

      .summary-row:last-child .summary-value {
        font-size: 20px;
      }

      #submit-button {
        padding: 14px;
        font-size: 15px;
      }

      .form-row {
        grid-template-columns: 1fr;
        gap: 18px;
      }
    }

    @media (max-width: 480px) {
      .checkout-card {
        padding: 18px;
      }

      .form-input {
        font-size: 14px;
        padding: 10px 12px;
      }
    }
  </style>
</head>
<body>
<div class="page-wrapper">
  <div class="page-header">
    <h1>Secure Checkout</h1>
    <p>Complete your payment securely</p>
  </div>

  <div class="checkout-container">
    <form id="checkout-form" onsubmit="return false;">
      <div class="checkout-grid">
      <!-- Payment Section (Left) -->
      <div class="checkout-card">
        <div class="card-header">
          <div class="card-icon">💳</div>
          <h2>Payment Details</h2>
        </div>

        <div class="order-summary">
          <div class="summary-row">
            <span class="summary-label">Order Number</span>
            <span class="summary-value">#{{ $order->id }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Appointment</span>
            <span class="summary-value">#{{ $appointment->id }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Total Amount</span>
            <span class="summary-value">£{{ number_format($order->amount / 100, 2) }}</span>
          </div>
        </div>

        <div id="sp-container"></div>

        <div class="security-badge">
          Secured by 256-bit SSL encryption
        </div>
      </div>

      <!-- Customer Information Section (Right) -->
      <div class="checkout-card">
        <div class="card-header">
          <div class="card-icon">👤</div>
          <h2>Customer Information</h2>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="first_name">First Name<span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" class="form-input" value="{{ $customer->name }}" placeholder="John" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="last_name">Last Name<span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" class="form-input" value="{{ $customer->last_name }}" placeholder="Doe" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email Address<span class="required">*</span></label>
          <input type="email" id="email" name="email" class="form-input" value="{{ $customer->email }}" placeholder="john.doe@example.com" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="contact">Contact Number<span class="required">*</span></label>
          <input type="tel" id="contact" name="contact" class="form-input" value="{{ $customer->contact }}" placeholder="+44 7700 900000" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="billing_address">Billing Address<span class="required">*</span></label>
          <input type="text" id="billing_address" name="billing_address" class="form-input" value="{{ $customer->billing_address }}" placeholder="123 Main Street" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="city">City<span class="required">*</span></label>
            <input type="text" id="city" name="city" class="form-input" value="{{ $customer->city }}" placeholder="London" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="postal_code">Postal Code<span class="required">*</span></label>
            <input type="text" id="postal_code" name="postal_code" class="form-input" value="{{ $customer->postal_code }}" placeholder="SW1A 1AA" required>
          </div>
        </div>
      </div>
    </div>

    <div class="checkout-card" style="max-width: 100%;">
      <button id="submit-button" type="button" disabled>Loading…</button>
      
      <div id="opayo-errors" class="opayo-error"></div>
      <div id="opayo-warning" class="opayo-warning"></div>
    </div>
  </form>
  </div>
</div>

<!-- 3DS Challenge Form (hidden, auto-submits when 3DS required) -->
<div id="threeds-container" style="display: none;">
  <form id="threeds-form" method="POST" action="" target="_self">
    <input type="hidden" name="creq" id="creq-input" value="">
    <input type="hidden" name="threeDSSessionData" id="threeds-session-input" value="">
  </form>
  <div class="redirect-message">
    <div class="spinner"></div>
    <p><strong>Redirecting to your bank for secure authentication</strong></p>
    <p>Please do not close this window or press the back button.</p>
  </div>
</div>

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


 

  const firstName = document.getElementById("first_name");
  const lastName = document.getElementById("last_name");
  const email = document.getElementById("email");
  const contact = document.getElementById("contact");
  const billingAddress = document.getElementById("billing_address");
  const city = document.getElementById("city");
  const postalCode = document.getElementById("postal_code");


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
      first_name: firstName.value,
      last_name: lastName.value,
      email: email.value,
      contact: contact.value,
      billing_address: billingAddress.value,
      city: city.value,
      postal_code: postalCode.value,
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
    if(!firstName.value || !lastName.value || !email.value || !contact.value || !billingAddress.value || !city.value || !postalCode.value){
      alert("Please fill in all the fields");
      return;
    }
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