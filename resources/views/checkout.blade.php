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
    .amount{font-size:22px;margin-bottom:25px;}
    .payment{font-size:23px;margin-bottom:25px;}
    #submit-button{padding:12px 30px;background:#007bff;color:#fff;border:none;border-radius:4px;font-size:16px;cursor:pointer;}
    #submit-button:disabled{background:#ccc;}
    .opayo-error{display:none;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px;border-radius:4px;margin-top:20px;}
    pre#debug{background:#f8f9fa;border:1px solid #ddd;padding:10px;font-size:12px;max-height:200px;overflow:auto;}
    #threeds-form{display:none;}
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
</div>

<!-- 3DS Challenge Form (hidden, auto-submits when 3DS required) -->
<form id="threeds-form" method="POST" action="" target="_self">
  <input type="hidden" name="creq" id="creq-input" value="">
  <input type="hidden" name="threeDSSessionData" id="threeds-session-input" value="">
  <p>Redirecting to your bank for authentication...</p>
</form>

<pre id="debug"></pre>

<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>

<script>
(async function(){
  const orderId = {{ $order->id }};
  const appointmentId = {{ $appointment->id }};
  const msk = "{{ $merchantSessionKey }}";

  const submitBtn = document.getElementById("submit-button");
  const errorBox = document.getElementById("opayo-errors");
  const debugEl = document.getElementById("debug");

  function debug(...args){console.log(...args); debugEl.textContent += args.map(x=>typeof x==="object"?JSON.stringify(x,null,2):x).join(" ")+"\n";}
  function showError(msg){errorBox.style.display="block"; errorBox.innerHTML="<b>Error:</b> "+msg;}

  if(!window.sagepayCheckout){showError("Drop-In script failed to load."); return;}
  if(!msk){showError("Missing Merchant Session Key."); return;}

  let checkout = null;
  try{
    checkout = sagepayCheckout({
      merchantSessionKey: msk,
      onTokenise: onToken,
    });
    checkout.form("#sp-container");
    submitBtn.disabled=false;
    submitBtn.textContent="Pay Now";
    debug("Drop-In mounted");
  }catch(e){debug("Mount failed:",e);showError("Payment widget failed to load.");return;}

  async function onToken(result){
    debug("Token callback:", result);
    if(!result.success){
      showError(result.error?.errorMessage||"Tokenisation failed"); 
      submitBtn.disabled=false; 
      submitBtn.textContent="Pay Now"; 
      return;
    }
    await processPayment(result.cardIdentifier);
  }

  async function processPayment(cardIdentifier){
    const payload = {
      appointment_id: appointmentId,
      order_id: orderId,
      merchantSessionKey: msk,
      cardIdentifier,
      strongCustomerAuthentication: {
        browserJavaEnabled: navigator.javaEnabled(),
        browserColorDepth: String(screen.colorDepth),
        browserScreenHeight: String(screen.height),
        browserScreenWidth: String(screen.width),
        browserTZ: String(new Date().getTimezoneOffset()),
        browserUserAgent: navigator.userAgent,
        browserLanguage: navigator.language,
        browserAcceptHeader: "text/html,application/json",
        browserJavascriptEnabled: true,
        notificationURL: "{{ route('handle3DSNotification') }}"
      }
    };

    debug("Sending payload to backend:", payload);

    try{
      const response = await fetch("{{ url('/api/transactions') }}",{
        method:"POST",
        headers:{
          "Content-Type":"application/json",
          "Accept":"application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
        },
        body:JSON.stringify(payload)
      });

      const data = await response.json();
      debug("Backend response:", data);

      // Check if 3DS authentication is required
      if (data.body && data.body.status === "3DAuth") {
        debug("3DS authentication required");
        
        // Validate required 3DS fields
        if (!data.body.acsUrl || !data.body.cReq) {
          throw new Error("Missing 3DS authentication data");
        }

        // Prepare threeDSSessionData - use transactionId to track the transaction
        const threeDSSessionData = btoa(data.body.transactionId);
        
        // Populate the hidden form
        document.getElementById("threeds-form").action = data.body.acsUrl;
        document.getElementById("creq-input").value = data.body.cReq;
        document.getElementById("threeds-session-input").value = threeDSSessionData;
        
        // Hide checkout, show 3DS redirect message
        document.querySelector(".checkout-container").style.display = "none";
        document.getElementById("threeds-form").style.display = "block";
        
        debug("Redirecting to ACS URL:", data.body.acsUrl);
        
        // Auto-submit the form to redirect to bank's authentication page
        document.getElementById("threeds-form").submit();
        return;
      }

      // Check for authorization success (no 3DS required)
      if(data.body && data.body.status === "Ok"){
        alert("Payment successful!");
        debug("Payment success:", data);
        // Redirect to success page
        window.location.href = "{{ url('/payment/success') }}?order=" + orderId;
        return;
      }

      // Handle rejected or failed transactions
      if(data.status >= 400 || data.body?.status==="Rejected"){
        throw new Error(data.body?.statusDetail || "Payment failed");
      }

      // If we get here, something unexpected happened
      throw new Error("Unexpected payment response");

    }catch(error){
      console.error("Payment error:", error);
      showError(error.message || "An error occurred during payment");
      submitBtn.disabled=false;
      submitBtn.textContent="Pay Now";
    }
  }

  submitBtn.addEventListener("click", async ()=>{
    submitBtn.disabled=true;
    submitBtn.textContent="Processing…";
    errorBox.style.display="none";
    try{
      await checkout.tokenise();
    }catch(error){
      console.error("Tokenization error:",error);
      showError("Failed to process payment. Please check your card details and try again.");
      submitBtn.disabled=false;
      submitBtn.textContent="Pay Now";
    }
  });

})();
</script>
</body>
</html>