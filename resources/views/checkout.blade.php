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

<pre id="debug"></pre>

<!-- Sage Pay Sandbox Drop-In -->
<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>

<script>
(async function () {
    const orderId = {{ $order->id }};
    const appointmentId = {{ $appointment->id }};
    const msk = "{{ $merchantSessionKey }}";

    const submitBtn = document.getElementById("submit-button");
    const errorBox = document.getElementById("opayo-errors");
    const debugEl = document.getElementById("debug");

    function debug(...args) {
        console.log(...args);
        debugEl.textContent += args.map(x => typeof x === "object" ? JSON.stringify(x,null,2) : x).join(" ") + "\n";
    }

    function showError(msg) {
        errorBox.style.display = "block";
        errorBox.innerHTML = "<b>Error:</b> " + msg;
    }

    if (!window.sagepayCheckout) {
        showError("Drop-in script failed to load.");
        return;
    }

    if (!msk) {
        showError("Missing Merchant Session Key.");
        return;
    }

    let checkout = null;

    try {
        checkout = sagepayCheckout({
            merchantSessionKey: msk,
            onTokenise: onToken
        });
        checkout.form("#sp-container");
        submitBtn.disabled = false;
        submitBtn.textContent = "Pay Now";
        debug("Drop-In mounted");
    } catch (e) {
        debug("Mount failed:", e);
        showError("Payment widget failed to load.");
        return;
    }

    // Handles Drop-In tokenisation callback (including 3DS)
    async function onToken(result) {
        debug("Token callback:", result);

        if (!result.success) {
            showError(result.error?.errorMessage || "Tokenisation failed");
            submitBtn.disabled = false;
            submitBtn.textContent = "Pay Now";
            return;
        }

        if (result.requires3DS) {
            debug("3DS authentication required, Drop-In will handle it automatically");
            // Drop-In handles the 3DS challenge UI
            return;
        }

        // If 3DS not required or already completed
        await processPayment(result.cardIdentifier);
    }

    // Sends token to backend to process payment
    async function processPayment(cardIdentifier) {
        const payload = {
            appointment_id: appointmentId,
            order_id: orderId,
            merchantSessionKey: msk,
            cardIdentifier
        };

        debug("Sending payload to backend:", payload);

        try {
            const response = await fetch("{{ url('/api/transactions') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify(payload)
            });

            let data = {};
            const contentType = response.headers.get("content-type") || "";
            if (contentType.includes("application/json")) {
                data = await response.json();
            } else {
                const text = await response.text();
                debug("Backend returned non-JSON:", text);
                throw new Error("Invalid response from backend.");
            }

            debug("Backend response:", data);

            // Backend indicates 3DS required
            if (data.body?.requires_3ds && data.body?.three_ds_data) {
                debug("3DS authentication required from backend, triggering Drop-In handle3DS...");
                await checkout.handle3DS(data.body.three_ds_data);
                return;
            }

            // Payment rejected
            if (data.body?.status === "Rejected" || data.body?.statusCode) {
                const detail = data.body?.statusDetail || "Payment failed";
                showError(`Payment rejected: ${detail}`);
                submitBtn.disabled = false;
                submitBtn.textContent = "Try Again";
                return;
            }

            // Payment successful
            console.log("Payment success:", data);
            alert("Payment successful!");
            const successUrl = `myapp://payment-success?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&data=${encodeURIComponent(JSON.stringify(data || {}))}`;
            window.location.href = successUrl;

        } catch (error) {
            console.error("Payment error:", error);
            showError(error.message || "An error occurred during payment");
            submitBtn.disabled = false;
            submitBtn.textContent = "Try Again";

            const failUrl = `myapp://payment-failed?order_id=${encodeURIComponent(orderId)}&appointment_id=${encodeURIComponent(appointmentId)}&data=${encodeURIComponent(JSON.stringify(error || {}))}`;
            window.location.href = failUrl;
        }
    }

    // Trigger Drop-In tokenisation on button click
    submitBtn.addEventListener("click", async () => {
        submitBtn.disabled = true;
        submitBtn.textContent = "Processing…";

        try {
            await checkout.tokenise();
        } catch (error) {
            console.error("Tokenization error:", error);
            showError("Failed to process payment. Please try again.");
            submitBtn.disabled = false;
            submitBtn.textContent = "Pay Now";
        }
    });

})();
</script>

</body>
</html>
