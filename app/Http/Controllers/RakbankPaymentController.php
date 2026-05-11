<?php

namespace App\Http\Controllers;

use App\Models\IncompleteOrder;
use App\Models\Order;
use App\Services\RakbankPaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class RakbankPaymentController extends Controller
{
    protected $rakbankService;
    protected $orderService;

    public function __construct(RakbankPaymentService $rakbankService, OrderService $orderService)
    {
        $this->rakbankService = $rakbankService;
        $this->orderService = $orderService;
    }

    /**
     * Show the checkout bridge page
     */
    public function pay($incompleteOrderId)
    {
        $incompleteOrder = IncompleteOrder::findOrFail($incompleteOrderId);

        // 1. Initiate Checkout Session
        // Note: initiateCheckout expects an object with invoice_no and total
        $sessionData = $this->rakbankService->initiateCheckout($incompleteOrder);

        if (!$sessionData || !isset($sessionData['session']['id'])) {
            return redirect()->route('checkout.index')->with('error', 'Unable to initiate payment. Please try again or choose another method.');
        }

        $sessionId = $sessionData['session']['id'];
        $merchantId = config('services.rakbank.merchant_id');

        return view('frontend.rakbank.pay', [
            'order' => $incompleteOrder, // The view might expect 'order' variable
            'sessionId' => $sessionId,
            'merchantId' => $merchantId
        ]);
    }

    /**
     * Handle the return from RAKBANK
     */
    public function callback(Request $request)
    {
        $incompleteOrderId = $request->query('order_id'); // This is the ID we passed to the gateway
        $incompleteOrder = IncompleteOrder::findOrFail($incompleteOrderId);

        // Query the API to confirm payment status
        $orderData = $this->rakbankService->retrieveOrder($incompleteOrder->invoice_no);

        $gatewayStatus = $orderData['order']['status'] ?? null;
        $result        = $orderData['result'] ?? null;

        Log::info('RAKBANK Callback Received', [
            'incomplete_order_id' => $incompleteOrder->id,
            'gateway_status'      => $gatewayStatus,
            'result'              => $result,
            'full_response'       => $orderData,
        ]);

        $successStatuses = ['CAPTURED', 'AUTHORIZED', 'PURCHASED'];

        if ($orderData && (in_array($gatewayStatus, $successStatuses) || $result === 'SUCCESS')) {
            
            // Payment Successful - Create real order if not already created
            if ($incompleteOrder->status === 'pending') {
                $order = DB::transaction(function () use ($incompleteOrder, $orderData, $gatewayStatus) {
                    $transactionId  = $orderData['transaction'][0]['transaction']['id']
                        ?? $orderData['transaction']['id']
                        ?? null;
                    $gatewayOrderId = $orderData['order']['id'] ?? null;

                    // 1. Create real order
                    $order = $this->orderService->createOrder(
                        $incompleteOrder->customer_data,
                        $incompleteOrder->cart_data,
                        $incompleteOrder->totals_data,
                        'rakbank',
                        $incompleteOrder->coupon_data,
                        $incompleteOrder->user_id
                    );

                    // 2. Update order with payment details
                    $order->update([
                        'status'          => 'processing',
                        'paid_amount'     => $order->total,
                        'due_amount'      => 0,
                        'transaction_id'  => $transactionId,
                        'gateway_order_id'=> $gatewayOrderId,
                        'notes'           => ($order->notes ? $order->notes . "\n" : "")
                            . "RAKBANK Payment {$gatewayStatus}. Txn: {$transactionId}",
                    ]);

                    // 3. Mark incomplete order as completed
                    $incompleteOrder->update([
                        'status'   => 'completed',
                        'order_id' => $order->id
                    ]);

                    return $order;
                });

                // Clear cart session
                \Illuminate\Support\Facades\Session::forget('cart');
                \Illuminate\Support\Facades\Session::forget('coupon');

                // Send Success Email Notification
                if ($order->guest_email) {
                    try {
                        Notification::route('mail', $order->guest_email)
                            ->notify(new PaymentSuccessNotification($order));
                    } catch (\Exception $e) {
                        Log::error('Failed to send PaymentSuccessNotification for order ' . $order->invoice_no, [
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                session()->put('placed_order_id', $order->id);
                return redirect()->route('checkout.success', $order->id)->with('success', 'Payment successful!');
            } else {
                // Already processed (maybe by webhook)
                $orderId = $incompleteOrder->order_id;
                return redirect()->route('checkout.success', $orderId)->with('success', 'Payment successful!');
            }
        }

        Log::warning('RAKBANK Payment Verification Failed or Cancelled', [
            'incomplete_order_id' => $incompleteOrder->id,
            'gateway_status'      => $gatewayStatus,
            'result'              => $result,
            'gateway_data'        => $orderData,
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

        // 2. Extract Invoice Number and Status
        $invoiceNo = $payload['order']['id'] ?? null;
        if (!$invoiceNo) {
            return response()->json(['status' => 'error', 'message' => 'Invoice number missing'], 400);
        }

        $incompleteOrder = IncompleteOrder::where('invoice_no', $invoiceNo)->first();
        if (!$incompleteOrder) {
            Log::error('RAKBANK Webhook: Incomplete Order not found', ['invoice_no' => $invoiceNo]);
            return response()->json(['status' => 'error', 'message' => 'Incomplete Order not found'], 404);
        }

        // 3. Update Order Status if Payment Success
        $gatewayStatus = $payload['order']['status'] ?? null;
        $result        = $payload['result'] ?? null;
        
        if ($gatewayStatus === 'CAPTURED' || $gatewayStatus === 'AUTHORIZED' || $result === 'SUCCESS') {
            // Only update if not already processed
            if ($incompleteOrder->status === 'pending') {
                $order = DB::transaction(function () use ($incompleteOrder, $payload, $gatewayStatus) {
                    $transactionId  = $payload['transaction']['id'] ?? null;
                    $gatewayOrderId = $payload['order']['id'] ?? null;

                    // 1. Create real order
                    $order = $this->orderService->createOrder(
                        $incompleteOrder->customer_data,
                        $incompleteOrder->cart_data,
                        $incompleteOrder->totals_data,
                        'rakbank',
                        $incompleteOrder->coupon_data,
                        $incompleteOrder->user_id
                    );

                    // 2. Update order with payment details
                    $order->update([
                        'status'          => 'processing',
                        'paid_amount'     => $order->total,
                        'due_amount'      => 0,
                        'transaction_id'  => $transactionId,
                        'gateway_order_id'=> $gatewayOrderId,
                        'notes'           => ($order->notes ? $order->notes . "\n" : "")
                            . "RAKBANK Webhook: {$gatewayStatus}. Txn: {$transactionId}",
                    ]);

                    // 3. Mark incomplete order as completed
                    $incompleteOrder->update([
                        'status'   => 'completed',
                        'order_id' => $order->id
                    ]);

                    return $order;
                });

                // Send Success Email Notification
                if ($order->guest_email) {
                    try {
                        Notification::route('mail', $order->guest_email)
                            ->notify(new PaymentSuccessNotification($order));
                    } catch (\Exception $e) {
                        Log::error('Failed to send PaymentSuccessNotification via Webhook for order ' . $order->invoice_no, [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
