<!DOCTYPE html>
<html>
<head>
    <title>PayHere Hash Tester</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            color: #4CAF50;
            margin-top: 0;
        }
        .code-block {
            background: #f8f8f8;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #4CAF50;
            color: white;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #45a049;
        }
        .btn-test {
            background: #2196F3;
        }
        .btn-test:hover {
            background: #0b7dda;
        }
    </style>
    <script src="https://www.payhere.lk/lib/payhere.js"></script>
</head>
<body>

<h1>🔍 PayHere Integration Debugger</h1>

<div class="test-section">
    <h2>❗ Common Issues Checklist</h2>
    <div class="warning">
        <strong>Before testing, verify these in your PayHere account:</strong>
        <ol>
            <li><strong>Domain/App Registration:</strong> Go to Side Menu → Integrations → Check if your domain is listed</li>
            <li><strong>Approval Status:</strong> Your domain must show "Approved" status (takes up to 24 hours)</li>
            <li><strong>Correct Merchant Secret:</strong> Copy the secret from the EXACT row of your registered domain</li>
            <li><strong>Sandbox Mode:</strong> Ensure you're using sandbox merchant ID and secret for testing</li>
        </ol>
    </div>
</div>

<div class="test-section">
    <h2>📊 Configuration Values</h2>
    <table>
        <tr>
            <th>Parameter</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Merchant ID</td>
            <td><code>{{ $config['merchant_id'] }}</code></td>
        </tr>
        <tr>
            <td>Mode</td>
            <td><code>{{ $config['mode'] }}</code></td>
        </tr>
        <tr>
            <td>Merchant Secret (Raw)</td>
            <td><code style="word-break: break-all;">{{ $config['merchant_secret_raw'] }}</code></td>
        </tr>
        <tr>
            <td>Merchant Secret (Decoded)</td>
            <td><code style="word-break: break-all;">{{ $config['merchant_secret_decoded'] }}</code></td>
        </tr>
        <tr>
            <td>Is Base64?</td>
            <td><code>{{ $config['is_base64'] ? 'YES' : 'NO' }}</code></td>
        </tr>
    </table>
</div>

<div class="test-section">
    <h2>🔐 Hash Generation Tests</h2>
    <p><strong>Test Order ID:</strong> <code>{{ $hashes['order_id'] }}</code></p>
    <p><strong>Test Amount:</strong> <code>{{ $hashes['amount'] }} {{ $hashes['currency'] }}</code></p>
    
    <h3>Method 1: Without Base64 Decode (Raw Secret)</h3>
    <div class="code-block">
        md5_of_secret = {{ $hashes['md5_raw'] }}<br>
        <strong>HASH = {{ $hashes['hash_no_decode'] }}</strong>
    </div>
    
    <h3>Method 2: With Base64 Decode</h3>
    <div class="code-block">
        md5_of_secret = {{ $hashes['md5_decoded'] }}<br>
        <strong>HASH = {{ $hashes['hash_with_decode'] }}</strong>
    </div>
</div>

<div class="test-section">
    <h2>🧪 Live Payment Tests</h2>
    <p>Click each button to test with different hash methods:</p>
    
    <button class="btn btn-test" id="test-no-decode">Test Method 1 (No Decode)</button>
    <button class="btn btn-test" id="test-with-decode">Test Method 2 (With Decode)</button>
    
    <div id="test-result" style="margin-top: 20px;"></div>
</div>

<div class="test-section">
    <h2>📝 Next Steps if Still Failing</h2>
    <div class="error">
        <strong>If both methods fail with "Unauthorized payment request":</strong>
        <ol>
            <li>Go to PayHere Dashboard → Side Menu → Integrations</li>
            <li>Click "Add Domain/App"</li>
            <li>For localhost testing, add: <code>localhost</code> or <code>127.0.0.1</code></li>
            <li>Wait for approval (can take 1-24 hours)</li>
            <li>Once approved, copy the NEW Merchant Secret from that row</li>
            <li>Update your .env file with the NEW secret</li>
            <li>Run: <code>php artisan config:clear</code></li>
        </ol>
    </div>
</div>

<script>
    var payment1 = @json($payment1);
    var payment2 = @json($payment2);

    payhere.onCompleted = function (orderId) {
        showResult("✅ Payment Completed! Order ID: " + orderId, "success");
    };

    payhere.onDismissed = function () {
        showResult("⚠️ Payment dismissed by user", "warning");
    };

    payhere.onError = function (error) {
        showResult("❌ PayHere Error: " + error, "error");
    };

    document.getElementById('test-no-decode').onclick = function () {
        showResult("🔄 Testing Method 1 (No Decode)...", "warning");
        setTimeout(() => payhere.startPayment(payment1), 500);
    };

    document.getElementById('test-with-decode').onclick = function () {
        showResult("🔄 Testing Method 2 (With Decode)...", "warning");
        setTimeout(() => payhere.startPayment(payment2), 500);
    };

    function showResult(message, type) {
        var resultDiv = document.getElementById('test-result');
        resultDiv.className = type;
        resultDiv.innerHTML = '<strong>' + message + '</strong>';
    }
</script>

</body>
</html>
