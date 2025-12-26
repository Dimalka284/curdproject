<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PayHereService;

class PayHereTestController extends Controller
{
    public function index()
    {
        $merchant_id     = config('payhere.merchant_id');
        $merchant_secret = config('payhere.merchant_secret');
        $return_url = config('payhere.return_url');
        $cancel_url = config('payhere.cancel_url');
        $notify_url = config('payhere.notify_url');
        $order_id = uniqid('ORD_');
        $amount   = number_format(1000, 2, '.', '');
        $currency = 'LKR';

        // 1. Hash the merchant secret (important for security and PayHere API requirements)
        $hashedSecret = strtoupper(md5($merchant_secret));

        // 2. Generate the PayHere hash
        $hash = strtoupper(md5(
            $merchant_id .
            $order_id .
            $amount .
            $currency .
            $hashedSecret
        ));

        Log::info('PaymentController Debug', [
            'merchant_id' => $merchant_id,
            'order_id' => $order_id,
            'amount' => $amount,
            'currency' => $currency,
            'hashedSecret' => $hashedSecret,    
            'generated_hash' => $hash,
        ]);

        $paymentData = [
            'sandbox'      => config('payhere.mode', 'sandbox') === 'sandbox',
            'merchant_id'  => $merchant_id,
            'return_url'   => $return_url,
            'cancel_url'   => $cancel_url,
            'notify_url'   => $notify_url,
            'order_id'     => $order_id,
            'items'        => 'Order Payment',
            'amount'       => $amount,
            'currency'     => $currency,
            'hash'         => $hash,
            'first_name'   => 'Test',
            'last_name'    => 'User',
            'email'        => 'test@test.com',
            'phone'        => '0771234567',
            'address'      => 'No 1, Galle Road',
            'city'         => 'Colombo',
            'country'      => 'Sri Lanka',
        ];

        return view('payhere.test', compact('paymentData'));
    }

    public function debugger()
    {
        $merchantId     = config('payhere.merchant_id');
        $merchantSecret = config('payhere.merchant_secret');
        $mode           = config('payhere.mode', 'sandbox');
        
        $orderId  = 'DEBUG_' . uniqid();
        $amount   = 2000.00;
        $currency = 'LKR';
        $amountFormatted = number_format($amount, 2, '.', '');
        
        // Test if merchant secret is Base64
        $decodedSecret = base64_decode($merchantSecret, true);
        $isBase64 = ($decodedSecret !== false && base64_encode($decodedSecret) === $merchantSecret);
        
        // Hash Method 1: No decode (use raw)
        $md5Raw = strtoupper(md5($merchantSecret));
        $hashNoD = strtoupper(md5($merchantId . $orderId . $amountFormatted . $currency . $md5Raw));
        
        // Hash Method 2: With decode
        $actualDecoded = base64_decode($merchantSecret);
        $md5Decoded = strtoupper(md5($actualDecoded));
        $hashWithDecode = strtoupper(md5($merchantId . $orderId . $amountFormatted . $currency . $md5Decoded));
        
        $config = [
            'merchant_id' => $merchantId,
            'mode' => $mode,
            'merchant_secret_raw' => $merchantSecret,
            'merchant_secret_decoded' => $actualDecoded,
            'is_base64' => $isBase64
        ];
        
        $hashes = [
            'order_id' => $orderId,
            'amount' => $amountFormatted,
            'currency' => $currency,
            'md5_raw' => $md5Raw,
            'md5_decoded' => $md5Decoded,
            'hash_no_decode' => $hashNoD,
            'hash_with_decode' => $hashWithDecode
        ];
        
        // Payment 1: No decode
        $payment1 = [
            'sandbox' => $mode === 'sandbox',
            'merchant_id' => $merchantId,
            'return_url' => config('payhere.return_url'),
            'cancel_url' => config('payhere.cancel_url'),
            'notify_url' => config('payhere.notify_url'),
            'order_id' => $orderId,
            'items' => 'Debug Test - No Decode',
            'amount' => $amountFormatted,
            'currency' => $currency,
            'hash' => $hashNoD,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '0771234567',
            'address' => 'No.1, Galle Road',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
        ];
        
        // Payment 2: With decode
        $payment2 = $payment1;
        $payment2['hash'] = $hashWithDecode;
        $payment2['items'] = 'Debug Test - With Decode';
        
        return view('payhere.debugger', compact('config', 'hashes', 'payment1', 'payment2'));
    }

    public function returnUrl(Request $request)
    {
        Log::info('PayHere Return', $request->all());
        return "Payment Successful! Order ID: " . $request->order_id;
    }

    public function cancelUrl(Request $request)
    {
        Log::info('PayHere Cancel', $request->all());
        return "Payment Cancelled!";
    }

    public function notifyUrl(Request $request)
    {
        Log::info('PayHere Notify', $request->all());

        // Get payment notification data
        $merchant_id      = $request->input('merchant_id');
        $order_id         = $request->input('order_id');
        $payhere_amount   = $request->input('payhere_amount');
        $payhere_currency = $request->input('payhere_currency');
        $status_code      = $request->input('status_code');
        $md5sig           = $request->input('md5sig');

        // Get merchant secret (use raw, no decode)
        $merchantSecret = config('payhere.merchant_secret');

        // Generate local md5sig for verification
        $local_md5sig = strtoupper(
            md5(
                $merchant_id .
                $order_id .
                $payhere_amount .
                $payhere_currency .
                $status_code .
                strtoupper(md5($merchantSecret))
            )
        );

        // Verify the payment
        if (($local_md5sig === $md5sig) && ($status_code == 2)) {
            // Payment is successful and verified
            Log::info('PayHere Payment Success', [
                'order_id' => $order_id,
                'amount' => $payhere_amount,
                'currency' => $payhere_currency
            ]);

            // TODO: Update your database as payment success

            return response('OK', 200);
        } elseif ($local_md5sig === $md5sig) {
            // Signature is valid but payment failed/pending/etc
            Log::warning('PayHere Payment Not Successful', [
                'order_id' => $order_id,
                'status_code' => $status_code,
                'status' => $this->getStatusMessage($status_code)
            ]);

            return response('OK', 200);
        } else {
            // Invalid signature - possible fraud attempt
            Log::error('PayHere Invalid Signature', [
                'expected' => $local_md5sig,
                'received' => $md5sig,
                'order_id' => $order_id
            ]);

            return response('Invalid signature', 400);
        }
    }

    private function getStatusMessage($status_code)
    {
        $statuses = [
            2 => 'success',
            0 => 'pending',
            -1 => 'canceled',
            -2 => 'failed',
            -3 => 'chargedback'
        ];

        return $statuses[$status_code] ?? 'unknown';
    }
}
