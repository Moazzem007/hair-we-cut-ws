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

  <div class="amount">
    Order #{{ $order->id }} — £{{ number_format($order->amount / 100, 2) }}
  </div>

  <div class="payment">Appointment #{{ $appointment->id }}</div>

  <form id="checkout-form" onsubmit="return false;">
    <div id="sp-container"></div>
    <button id="submit-button" type="button" disabled>Loading…</button>
  </form>

  <div id="opayo-errors" class="opayo-error"></div>
</div>

{{-- <pre id="debug" style="display: none"></pre> --}}
<pre id="debug"></pre>

<!-- ✅ Correct Sandbox Script -->
<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>

<!-- ❗ Live Script (keep commented) -->
{{-- <script src="https://pi-live.sagepay.com/api/v1/js/sagepay.js"></script> --}}

<script>
(function () {
  const orderId = {{ $order->id }};
  const appointmentId = {{ $appointment->id }};
  const msk = "{{ $merchantSessionKey }}";

  const submitBtn = document.getElementById("submit-button");
  const errorBox = document.getElementById("opayo-errors");
  const debugEl = document.getElementById("debug");

  function debug(...a){
    return;
    console.log(...a);
    debugEl.textContent += a.map(x => typeof x === "object" ? JSON.stringify(x,null,2) : x).join(" ")+"\n";
  }

  function showError(msg){
    errorBox.style.display="block";
    errorBox.innerHTML = "<b>Error:</b> "+msg;
  }

  if(!window.sagepayCheckout){
    showError("Drop-in script failed to load.");
    debug("sagepayCheckout missing");
    return;
  }

  if(!msk){
    showError("Missing Merchant Session Key.");
    debug("Missing MSK");
    return;
  }

  debug("Initializing checkout…");

  let checkout = null;

  try {
    checkout = sagepayCheckout({
      merchantSessionKey: msk,
      onTokenise: onToken
    });

    // 🟢 MUST mount using ONLY the selector string
    checkout.form("#sp-container");

    submitBtn.disabled = false;
    submitBtn.textContent = "Pay Now";

    debug("Mounted using form('#sp-container')");
  }
  catch(e){
    debug("Mount failed:", e);
    showError("Payment widget failed to load.");
    return;
  }


 // Replace the existing onToken function with this:
function onToken(result) {
    debug("Token callback:", result);

    if (!result.success) {
        showError(result.error.errorMessage || "Tokenisation failed");
        submitBtn.disabled = false;
        submitBtn.textContent = "Pay Now";
        return;
    }

    // If 3DS is required, the Drop-In will handle the flow
    if (result.requires3DS) {
        debug("3DS authentication required, Drop-In will handle the flow");
        // The Drop-In will automatically show the 3DS challenge if needed
        // and call onToken again after 3DS completion
        return;
    }

    // If we reach here, 3DS is not required or is already completed
    processPayment(result.cardIdentifier);
}

// Add this new function to handle the actual payment processing
async function processPayment(cardIdentifier) {
    const payload = {
      appointment_id: appointmentId,
      order_id: orderId,
      merchantSessionKey: msk,
      cardIdentifier: cardIdentifier
    };

    alert('process payment');

    debug("Sending to backend:", payload);
alert('calling /api/transactions');
    try {
        const response = await fetch("/api/transactions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify(payload)
        });
alert('called /api/transactions');

        const data = await response.json().catch(() => ({}));
        console.log(data);
        alert('data logged from /api/transactions');
        debug("Backend response:", response.json());

        if (!response.ok || data.error) {
            throw new Error(data.message || "Payment failed");
        }

        // Handle 3DS response if needed
        if (data.requires3DS) {
            debug("3DS authentication required from backend");
            // The Drop-In will handle the 3DS flow
            checkout.handle3DS(data.threeDSData);
            return;
        }
alert('3DS authentication required');
        // If we get here, payment was successful
        const url = `myapp://payment-success?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&data=${encodeURIComponent(JSON.stringify(data || {}))}`;
        window.location.href = url;
        alert('myapp://payment-success?order_id');
    } catch (error) {
      alert('myapp://payment-failed?order_id');
        const url = `myapp://payment-failed?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&data=${encodeURIComponent(JSON.stringify(error || {}))}`;
        window.location.href = url;
        console.error("Payment error:", error);
        showError(error.message || "An error occurred during payment");
        submitBtn.disabled = false;
        submitBtn.textContent = "Try Again";
    }
}

// Update the submit button click handler
submitBtn.addEventListener("click", async () => {
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing…";

    try {
        // This will trigger the onToken callback
        alert('This will trigger the onToken callback');
        const result = await checkout.tokenise();
        console.log(result);
        alert('This will trigger the onToken callback (Logged)');
        

        // If 3DS is required, the Drop-In will handle it
        if (result.requires3DS) {
            debug("3DS authentication required, showing challenge...");
            return;
        }
alert('calling processPayment');
        // If no 3DS required, process payment
        await processPayment(result.cardIdentifier);

    } catch (error) {
        console.error("Tokenization error:", error);
        showError("Failed to process payment. Please try again.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Pay Now";
    }
});


  submitBtn.addEventListener("click", ()=> {
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing…";
    checkout.tokenise();
  });

})();
</script>

</body>
</html>
