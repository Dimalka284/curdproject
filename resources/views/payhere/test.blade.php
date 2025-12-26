<!DOCTYPE html>
<html>
<head>
    <title>PayHere Test</title>
    <script src="https://www.payhere.lk/lib/payhere.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .debug-info {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .debug-info h3 {
            margin-top: 0;
            color: #333;
        }
        .debug-info pre {
            background: #fff;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 15px 32px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<h1>PayHere Sandbox Test</h1>

<div class="debug-info">
    <h3>🔍 Payment Debug Information</h3>
    <pre>{{ json_encode($paymentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
</div>

@if(isset($warning) && $warning)
    <div class="alert alert-error">
        ⚠️ {{ $warning }}
    </div>
@endif

<div class="alert alert-success">
    ✅ Hash is generated using <strong>decoded</strong> merchant secret.
</div>

<button id="payhere-payment">💳 Pay Now (LKR 1,000.00)</button>

<div id="status-messages"></div>

<script>
    payhere.onCompleted = function (orderId) {
        console.log("Payment completed: " + orderId);
        showMessage("Payment completed for Order ID: " + orderId, "success");
        setTimeout(function() {
            window.location.href = "{{ config('payhere.return_url') }}?order_id=" + orderId;
        }, 2000);
    };

    payhere.onDismissed = function () {
        console.log("Payment dismissed");
        showMessage("Payment was dismissed", "error");
    };

    payhere.onError = function (error) {
        console.error("PayHere Error: " + error);
        showMessage("PayHere Error: " + error, "error");
    };

    function showMessage(message, type) {
        var statusDiv = document.getElementById('status-messages');
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type;
        alertDiv.textContent = message;
        statusDiv.appendChild(alertDiv);
    }

    var payment = @json($paymentData);

    document.getElementById('payhere-payment').onclick = function () {
        console.log('Starting payment with data:', payment);
        payhere.startPayment(payment);
    };
</script>

</body>
</html>
