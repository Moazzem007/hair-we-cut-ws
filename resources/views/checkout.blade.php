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
      onTokenise: onToken
    });
    checkout.form("#sp-container");
    submitBtn.disabled=false;
    submitBtn.textContent="Pay Now";
    debug("Drop-In mounted");
  }catch(e){debug("Mount failed:",e);showError("Payment widget failed to load.");return;}

  async function onToken(result){
    debug("Token callback:", result);
    if(!result.success){showError(result.error?.errorMessage||"Tokenisation failed"); submitBtn.disabled=false; submitBtn.textContent="Pay Now"; return;}
    await processPayment(result.cardIdentifier);
  }

  async function processPayment(cardIdentifier){
    const payload = {
      appointment_id: appointmentId,
      order_id: orderId,
      merchantSessionKey: msk,
      cardIdentifier,
      browserInfo: { // strongCustomerAuthentication
        browserJavaEnabled: navigator.javaEnabled(),
        browserColorDepth: screen.colorDepth,
        browserScreenHeight: screen.height,
        browserScreenWidth: screen.width,
        browserTZ: new Date().getTimezoneOffset(),
        browserUserAgent: navigator.userAgent,
        browserLanguage: navigator.language,
        notificationURL: "{{ route('handle3DSNotification') }}" // backend endpoint
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

      if (data.status === 202 && data.body && data.body.acsUrl) {
    debug("3DS authentication required, invoking Drop-In 3DS handler");
    
    // Create the 3DS data object that the Drop-in expects
    const threeDSData = {
        acsUrl: data.body.acsUrl,
        acsTransId: data.body.acsTransId,
        dsTransId: data.body.dsTransId,
        cReq: data.body.cReq,
        threeDSSessionData: data.body.threeDSSessionData || '' // Add if available
    };

    try {
        const result = await checkout.threeDS(threeDSData, async (cRes) => {
            debug("cRes from 3DS challenge:", cRes);
            
            try {
                const challengeResp = await fetch("{{ route('handle3DSNotification') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        transactionId: data.body.transactionId,
                        cRes: cRes.cres // Note: it should be 'cres' (lowercase) as that's what Opayo returns
                    })
                });

                const challengeData = await challengeResp.json();
                debug("3DS challenge backend response:", challengeData);
                
                if (challengeData.body?.status === "Ok") {
                    alert("Payment successful!");
                    // Handle successful payment
                } else {
                    throw new Error(challengeData.body?.statusDetail || "3DS authentication failed");
                }
            } catch (error) {
                console.error("3DS processing error:", error);
                showError("Payment processing failed. Please try again.");
                submitBtn.disabled = false;
                submitBtn.textContent = "Pay Now";
            }
        });

        debug("3DS challenge result:", result);
    } catch (error) {
        console.error("3DS challenge error:", error);
        showError("3D Secure authentication failed. Please try again.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Pay Now";
    }
    return;
}

      if(data.status >= 400 || data.body?.status==="Rejected"){throw new Error(data.body?.statusDetail || "Payment failed");}

      alert("Payment successful!");
      debug("Payment success:", data);

    }catch(error){
      console.error("Payment error:", error);
      showError(error.message || "An error occurred during payment");
      submitBtn.disabled=false;
      submitBtn.textContent="Try Again";
    }
  }

  submitBtn.addEventListener("click", async ()=>{
    submitBtn.disabled=true;
    submitBtn.textContent="Processing…";
    try{await checkout.tokenise();}catch(error){console.error("Tokenization error:",error);showError("Failed to process payment. Please try again.");submitBtn.disabled=false;submitBtn.textContent="Pay Now";}
  });

})();
</script>
</body>
</html>
