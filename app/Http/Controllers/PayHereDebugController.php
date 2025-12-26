<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayHereDebugController extends Controller
{
    public function debug()
    {
        $merchantId     = config('payhere.merchant_id');
        $merchantSecret = config('payhere.merchant_secret');
        
        $orderId  = 'ItemNo12345';
        $amount   = 1000.00;
        $currency = 'LKR';
        
        $amountFormatted = number_format($amount, 2, '.', '');
        
        // Method 1: WITHOUT Base64 decode (use raw secret)
        $hash1 = strtoupper(md5(
            $merchantId .
            $orderId .
            $amountFormatted .
            $currency .
            strtoupper(md5($merchantSecret))
        ));
        
        // Method 2: WITH Base64 decode
        $decodedSecret = base64_decode($merchantSecret);
        $hash2 = strtoupper(md5(
            $merchantId .
            $orderId .
            $amountFormatted .
            $currency .
            strtoupper(md5($decodedSecret))
        ));
        
        $debugInfo = [
            'merchant_id' => $merchantId,
            'order_id' => $orderId,
            'amount' => $amountFormatted,
            'currency' => $currency,
            'merchant_secret_raw' => $merchantSecret,
            'merchant_secret_decoded' => $decodedSecret,
            'md5_of_raw_secret' => strtoupper(md5($merchantSecret)),
            'md5_of_decoded_secret' => strtoupper(md5($decodedSecret)),
            'hash_method_1_no_decode' => $hash1,
            'hash_method_2_with_decode' => $hash2,
        ];
        
        return view('payhere.debug', compact('debugInfo'));
    }
}
