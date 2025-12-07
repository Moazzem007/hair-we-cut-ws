<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .error-icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: shake 0.5s ease-out;
        }
        .error-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .message {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .error-details {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 32px;
            text-align: left;
        }
        .error-title {
            color: #991b1b;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .error-text {
            color: #dc2626;
            font-size: 14px;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
        }
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            <svg viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        
        <h1>Payment Failed</h1>
        <p class="message">We were unable to process your payment. Please try again or use a different payment method.</p>
        
        <div class="error-details">
            <div class="error-title">Error Details:</div>
            <div class="error-text">{{ $error }}</div>
        </div>
        
        <div class="actions">
            <button onclick="history.back()" class="btn btn-primary">Try Again</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>