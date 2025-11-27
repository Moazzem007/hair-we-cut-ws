<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Checkout - Order {{ $order->id }}</title>
  <script src="https://assets.opayo.cloud/assets/js/opayo-1.2.40.js" crossorigin="anonymous"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <h2>Pay Order #{{ $order->id }} — {{ number_format($order->amount/100,2) }} {{ $order->currency }}</h2>
  <div id="sp-container"></div>
  <script>
    (function(){
      const merchantSessionKey = "{{ $merchantSessionKey }}";
      const orderId = "{{ $order->id }}";

      const checkout = opayoCheckout({
        merchantSessionKey: merchantSessionKey,
        onTokenise: function(result) {
          if (result.success) {
            // send cardIdentifier to backend to create transaction
            fetch("/api/transactions", {
              method:"POST",
              headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({
                order_id: orderId,
                cardIdentifier: result.cardIdentifier,
                merchantSessionKey: merchantSessionKey
              })
            }).then(r => r.json()).then(async data=>{
              // if 3DS required, data will include acsUrl etc. handle accordingly (opayoCheckout may do this)
              // For simplicity redirect to return page for UX (backend saved status)
              window.location.href = "/payment-return?order=" + orderId;
            }).catch(err => {
              alert('Payment failed: ' + err);
            });
          } else {
            alert('Tokenisation failed: ' + result.error.errorMessage);
          }
        }
      });

      // Attach to form automatically
      checkout.form();
    })();
  </script>
</body>
</html>
