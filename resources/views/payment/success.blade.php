<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .message {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .order-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
            font-size: 14px;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }
        .amount {
            font-size: 18px !important;
            color: #10b981 !important;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>Payment Successful!</h1>
        <p class="message">Thank you for your payment. Your transaction has been completed successfully.</p>
        
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order Number</span>
                <span class="detail-value">#{{ $order->id }}</span>
            </div>
            @if($appointment)
            <div class="detail-row">
                <span class="detail-label">Appointment</span>
                <span class="detail-value">#{{ $appointment->id }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value amount">£{{ number_format($order->amount / 100, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status</span>
                <span class="detail-value" style="color: #10b981;">Paid</span>
            </div>
        </div>
        
        <a href="{{ url('/dashboard') }}" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>