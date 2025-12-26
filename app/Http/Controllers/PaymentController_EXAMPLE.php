<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PayHereService;

/**
 * Example controller showing how to use the PayHereService
 */
class PaymentController extends Controller
{
    protected $payhere;
    
    public function __construct(PayHereService $payhere)
    {
        $this->payhere = $payhere;
    }
    
    /**
     * Show payment page for an order
     */
    public function checkout($orderId)
    {
        // Get order from database
        // $order = Order::findOrFail($orderId);
        
        // For demo purposes, using dummy data
        $order = (object) [
            'id' => $orderId,
            'total' => 2500.00,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '0771234567',
            'customer_address' => 'No. 123, Main Street',
            'customer_city' => 'Colombo',
        ];
        
        // Prepare payment data using the service
        $paymentData = $this->payhere->preparePaymentData([
            'order_id' => 'ORD_' . $order->id,
            'amount' => $order->total,
            'currency' => 'LKR',
            'items' => 'Order #' . $order->id,
            'first_name' => explode(' ', $order->customer_name)[0],
            'last_name' => explode(' ', $order->customer_name)[1] ?? '',
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'address' => $order->customer_address,
            'city' => $order->customer_city,
            'country' => 'Sri Lanka',
        ]);
        
        return view('payments.checkout', compact('paymentData'));
    }
    
    /**
     * Handle payment notification from PayHere
     */
    public function notify(Request $request)
    {
        Log::info('PayHere Payment Notification', $request->all());
        
        // Extract notification data
        $merchantId = $request->input('merchant_id');
        $orderId = $request->input('order_id');
        $amount = $request->input('payhere_amount');
        $currency = $request->input('payhere_currency');
        $statusCode = $request->input('status_code');
        $md5sig = $request->input('md5sig');
        $paymentId = $request->input('payment_id');
        
        // Verify the payment signature
        if (!$this->payhere->verifyPayment($merchantId, $orderId, $amount, $currency, $statusCode, $md5sig)) {
            Log::error('Invalid PayHere Signature', [
                'order_id' => $orderId,
                'merchant_id' => $merchantId
            ]);
            return response('Invalid signature', 400);
        }
        
        // Signature is valid - process based on status
        if ($this->payhere->isSuccessful($statusCode)) {
            // Payment successful - update database
            Log::info('PayHere Payment Success', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'amount' => $amount
            ]);
            
            // TODO: Update your order in database
            // Order::where('order_id', $orderId)->update([
            //     'payment_status' => 'paid',
            //     'payment_id' => $paymentId,
            //     'paid_amount' => $amount,
            //     'paid_at' => now()
            // ]);
            
            // TODO: Send confirmation email
            // Mail::to($order->customer_email)->send(new PaymentSuccessEmail($order));
            
        } else {
            // Payment failed or pending
            Log::warning('PayHere Payment Not Successful', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'status' => $this->payhere->getStatusMessage($statusCode)
            ]);
            
            // TODO: Update order status accordingly
            // Order::where('order_id', $orderId)->update([
            //     'payment_status' => $this->payhere->getStatusMessage($statusCode)
            // ]);
        }
        
        return response('OK', 200);
    }
    
    /**
     * Handle return after payment (user sees this)
     */
    public function returnUrl(Request $request)
    {
        $orderId = $request->input('order_id');
        
        // TODO: Fetch order from database to show status
        // $order = Order::where('order_id', $orderId)->first();
        
        // Show success page
        return view('payments.success', [
            'order_id' => $orderId,
            'message' => 'Your payment is being processed. You will receive a confirmation email shortly.'
        ]);
    }
    
    /**
     * Handle payment cancellation
     */
    public function cancelUrl(Request $request)
    {
        Log::info('PayHere Payment Cancelled', $request->all());
        
        return view('payments.cancelled', [
            'message' => 'Payment was cancelled. Please try again or contact support.'
        ]);
    }
}
