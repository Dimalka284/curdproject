<?php

namespace App\Services;

/**
 * PayHere Payment Service
 * 
 * This service handles PayHere payment hash generation and verification
 */
class PayHereService
{
    private $merchantId;
    private $merchantSecret;
    private $mode;
    
    public function __construct()
    {
        $this->merchantId = config('payhere.merchant_id');
        $this->merchantSecret = config('payhere.merchant_secret');
        $this->mode = config('payhere.mode', 'sandbox');
    }
    
    /**
     * Generate hash for payment request
     * 
     * @param string $orderId
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public function generateHash($orderId, $amount, $currency = 'LKR')
    {
        $amountFormatted = number_format($amount, 2, '.', '');
        
        return strtoupper(md5(
            $this->merchantId .
            $orderId .
            $amountFormatted .
            $currency .
            strtoupper(md5($this->merchantSecret))
        ));
    }
    
    /**
     * Verify payment notification signature
     * 
     * @param string $merchantId
     * @param string $orderId
     * @param string $amount
     * @param string $currency
     * @param int $statusCode
     * @param string $md5sig
     * @return bool
     */
    public function verifyPayment($merchantId, $orderId, $amount, $currency, $statusCode, $md5sig)
    {
        $localSignature = strtoupper(
            md5(
                $merchantId .
                $orderId .
                $amount .
                $currency .
                $statusCode .
                strtoupper(md5($this->merchantSecret))
            )
        );
        
        return $localSignature === $md5sig;
    }
    
    /**
     * Prepare payment data array for PayHere
     * 
     * @param array $params
     * @return array
     */
    public function preparePaymentData(array $params)
    {
        $orderId = $params['order_id'];
        $amount = $params['amount'];
        $currency = $params['currency'] ?? 'LKR';
        
        $amountFormatted = number_format($amount, 2, '.', '');
        $hash = $this->generateHash($orderId, $amount, $currency);
        
        return [
            'sandbox' => $this->mode === 'sandbox',
            'merchant_id' => $this->merchantId,
            'return_url' => $params['return_url'] ?? config('payhere.return_url'),
            'cancel_url' => $params['cancel_url'] ?? config('payhere.cancel_url'),
            'notify_url' => $params['notify_url'] ?? config('payhere.notify_url'),
            'order_id' => $orderId,
            'items' => $params['items'],
            'amount' => $amountFormatted,
            'currency' => $currency,
            'hash' => $hash,
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'address' => $params['address'],
            'city' => $params['city'],
            'country' => $params['country'] ?? 'Sri Lanka',
            'delivery_address' => $params['delivery_address'] ?? null,
            'delivery_city' => $params['delivery_city'] ?? null,
            'delivery_country' => $params['delivery_country'] ?? null,
            'custom_1' => $params['custom_1'] ?? null,
            'custom_2' => $params['custom_2'] ?? null,
        ];
    }
    
    /**
     * Get payment status message
     * 
     * @param int $statusCode
     * @return string
     */
    public function getStatusMessage($statusCode)
    {
        $statuses = [
            2 => 'success',
            0 => 'pending',
            -1 => 'canceled',
            -2 => 'failed',
            -3 => 'chargedback'
        ];
        
        return $statuses[$statusCode] ?? 'unknown';
    }
    
    /**
     * Check if payment is successful
     * 
     * @param int $statusCode
     * @return bool
     */
    public function isSuccessful($statusCode)
    {
        return $statusCode == 2;
    }
}
