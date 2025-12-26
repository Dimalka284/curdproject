<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f0fdf4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 3rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: center; max-width: 400px; width: 100%; }
        h1 { color: #16a34a; margin-bottom: 1rem; }
        p { color: #374151; margin-bottom: 2rem; }
        .btn { background-color: #16a34a; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn:hover { background-color: #15803d; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed successfully.</p>
        <div style="margin-bottom: 20px; text-align: left; background: #f9fafb; padding: 1rem; border-radius: 8px;">
            <div style="margin-bottom: 5px;"><strong>Order ID:</strong> {{ $order_id }}</div>
            <?php if(isset($payment_id)): ?>
                <div><strong>Payment ID:</strong> {{ $payment_id }}</div>
            <?php endif; ?>
        </div>
        <a href="{{ route('payhere.test') }}" class="btn">Make Another Payment</a>
    </div>
</body>
</html>
