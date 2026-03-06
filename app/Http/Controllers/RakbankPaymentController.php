<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\RakbankPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RakbankPaymentController extends Controller
{
    protected $rakbankService;

    public function __construct(RakbankPaymentService $rakbankService)
    {
        $this->rakbankService = $rakbankService;
    }

    /**
     * Show the checkout bridge page
     */
    public function pay(Order $order)
    {
        // 1. Initiate Checkout Session
        $sessionData = $this->rakbankService->initiateCheckout($order);

        if (!$sessionData || !isset($sessionData['session']['id'])) {
            return redirect()->route('checkout.index')->with('error', 'Unable to initiate payment. Please try again or choose another method.');
        }

        $sessionId = $sessionData['session']['id'];
        $merchantId = config('services.rakbank.merchant_id');

        return view('frontend.rakbank.pay', compact('order', 'sessionId', 'merchantId'));
    }

    /**
     * Handle the return from RAKBANK
     */
    public function callback(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::findOrFail($orderId);

        // Query the API to confirm payment status (more secure than trusting URL params)
        $orderData = $this->rakbankService->retrieveOrder($order->invoice_no);

        // RAKBANK nests status under order.status (not top-level)
        // e.g. { "order": { "status": "CAPTURED", ... }, "result": "SUCCESS" }
        $gatewayStatus = $orderData['order']['status'] ?? null;
        $result        = $orderData['result'] ?? null;

        Log::info('RAKBANK Callback Received', [
            'order_id'       => $order->id,
            'gateway_status' => $gatewayStatus,
            'result'         => $result,
            'full_response'  => $orderData,
        ]);

        $successStatuses = ['CAPTURED', 'AUTHORIZED', 'PURCHASED'];

        if ($orderData && in_array($gatewayStatus, $successStatuses)) {
            // Payment Successful - update order only if still pending
            if ($order->status === 'pending') {
                $transactionId  = $orderData['transaction'][0]['transaction']['id']
                    ?? $orderData['transaction']['id']
                    ?? null;
                $gatewayOrderId = $orderData['order']['id'] ?? null;

                $order->update([
                    'status'          => 'processing',
                    'payment_method'  => 'rakbank',
                    'transaction_id'  => $transactionId,
                    'gateway_order_id'=> $gatewayOrderId,
                    'notes'           => ($order->notes ? $order->notes . "\n" : "")
                        . "RAKBANK Payment {$gatewayStatus}. Txn: {$transactionId}",
                ]);

                // Clear cart session for the user
                \Illuminate\Support\Facades\Session::forget('cart');
                \Illuminate\Support\Facades\Session::forget('coupon');
            }

            session()->put('placed_order_id', $order->id);
            return redirect()->route('checkout.success', $order->id)->with('success', 'Payment successful!');
        }

        Log::warning('RAKBANK Payment Verification Failed or Cancelled', [
            'order_id'       => $order->id,
            'gateway_status' => $gatewayStatus,
            'result'         => $result,
            'gateway_data'   => $orderData,
        ]);

        return redirect()->route('checkout.index')->with('error', 'Payment failed or was cancelled. Please try again.');
    }

    /**
     * Webhook for asynchronous payment notifications
     */
    public function webhook(Request $request)
    {
        // 1. Verify Shared Secret
        $secret = config('services.rakbank.webhook_secret');
        if ($request->header('X-Notification-Secret') !== $secret) {
            Log::error('RAKBANK Webhook: Invalid Secret', [
                'received' => $request->header('X-Notification-Secret'),
                'ip' => $request->ip()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        
        Log::info('RAKBANK Webhook Received', $payload);

        // 2. Extract Order ID and Status
        $invoiceNo = $payload['order']['id'] ?? null;
        if (!$invoiceNo) {
            return response()->json(['status' => 'error', 'message' => 'Invoice number missing'], 400);
        }

        $order = Order::where('invoice_no', $invoiceNo)->first();
        if (!$order) {
            Log::error('RAKBANK Webhook: Order not found', ['invoice_no' => $invoiceNo]);
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // 3. Update Order Status if Payment Success
        $gatewayStatus = $payload['order']['status'] ?? null;
        
        if ($gatewayStatus === 'CAPTURED' || $gatewayStatus === 'AUTHORIZED') {
            // Only update if not already processed
            if ($order->status === 'pending') {
                $transactionId  = $payload['transaction']['id'] ?? null;
                $gatewayOrderId = $payload['order']['id'] ?? null;

                $order->update([
                    'status'          => 'processing',
                    'payment_method'  => 'rakbank',
                    'transaction_id'  => $transactionId,
                    'gateway_order_id'=> $gatewayOrderId,
                    'notes'           => ($order->notes ? $order->notes . "\n" : "")
                        . "RAKBANK Webhook: {$gatewayStatus}. Txn: {$transactionId}",
                ]);

                // Clear cart if we can identify the session?
                // Webhooks are server-to-server, they don't have the user's session.
                // The cart is cleared in the manual callback as well.
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
