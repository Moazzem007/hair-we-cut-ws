<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Checkout - Order {{ $order->id }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; background: #f5f5f5; }
    .checkout-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .amount { font-size: 22px; margin-bottom: 25px; }
    #submit-button { padding: 12px 30px; background: #007bff; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
    #submit-button:disabled { background: #ccc; cursor: not-allowed; }
    .opayo-error { display: none; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 4px; margin-top: 20px; }
    pre#debug { background: #f8f9fa; border: 1px solid #ddd; padding: 10px; font-size: 12px; max-height: 200px; overflow: auto; }
  </style>
</head>
<body>

<div class="checkout-container">
  <h2>Complete Payment</h2>
  <div class="amount">Order #{{ $order->id }} — £{{ number_format($order->amount / 100, 2) }}</div>

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
  const msk = "{{ $merchantSessionKey }}";
  const submitBtn = document.getElementById("submit-button");
  const errorBox = document.getElementById("opayo-errors");
  const debugEl = document.getElementById("debug");

  function debug(...args) {
    console.log(...args);
    debugEl.textContent += args.map(x =>
      (typeof x === "object" ? JSON.stringify(x, null, 2) : x)
    ).join(" ") + "\\n";
  }

  function showError(msg) {
    errorBox.style.display = "block";
    errorBox.innerHTML = "<b>Error:</b> " + msg;
  }

  function hideError() {
    errorBox.style.display = "none";
  }

  if (!window.sagepayCheckout) {
    showError("Drop‑In script failed to load.");
    return;
  }
  if (!msk) {
    showError("Missing Merchant Session Key.");
    return;
  }

  let checkout;
  try {
    checkout = sagepayCheckout({ merchantSessionKey: msk });
    checkout.form("#sp-container");
    debug("Drop‑In mounted");
  } catch (e) {
    debug("Mount error:", e);
    showError("Payment widget failed to load.");
    return;
  }

  checkout.form({
    formSelector: "#sp-container",
    onFormReady: () => {
      debug("Form ready");
      submitBtn.disabled = false;
      submitBtn.textContent = "Pay Now";
    },
    onError: (err) => {
      debug("Form error:", err);
      showError("Payment widget error");
      submitBtn.disabled = false;
      submitBtn.textContent = "Pay Now";
    },
    onTokenised: async function (result) {
      debug("Tokenised:", result);
      hideError();
      if (!result.success) {
        showError(result.error?.errorMessage || "Tokenisation failed");
        submitBtn.disabled = false;
        submitBtn.textContent = "Pay Now";
        return;
      }
      await processPayment(result.cardIdentifier);
    }
  });

  async function processPayment(cardIdentifier) {
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing…";

    const payload = { order_id: orderId, merchantSessionKey: msk, cardIdentifier };

    try {
      const resp = await fetch("/api/transactions", {
        method: "POST",
        headers: {
          "Content-Type":"application/json",
          "Accept":"application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify(payload)
      });
      const data = await resp.json().catch(() => ({}));
      debug("Backend response:", resp.status, data);

      // If 3DS required: backend returns status 202 or a body with acsUrl / threeDSData
      if (resp.status === 202 || data.body?.acsUrl || data.body?.threeDSData) {
        debug("3DS required → redirecting to ACS:", data.body.acsUrl || data.body.threeDSData);
        window.location.href = data.body.acsUrl || data.body.threeDSData.redirectUrl;
        return;
      }

      // If rejected
      if (!resp.ok || data.status === "Rejected" || data.statusCode) {
        const err = data.statusDetail || data.message || "Payment rejected";
        throw new Error(err);
      }

      // Payment success
      debug("Payment success:", data);
      // Example: notify parent or redirect
      window.location.href = `/payment/success?order_id=${orderId}`;
    } catch (err) {
      debug("Payment error:", err);
      showError(err.message || "An error occurred");
      submitBtn.disabled = false;
      submitBtn.textContent = "Pay Now";
    }
  }

  submitBtn.addEventListener("click", () => {
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing…";
    checkout.tokenise();
  });

})();
</script>

</body>
</html>
