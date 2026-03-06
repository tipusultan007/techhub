<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakbankPaymentService
{
    protected $merchantId;
    protected $apiPassword;
    protected $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.rakbank.merchant_id');
        $this->apiPassword = config('services.rakbank.api_password');
        $this->baseUrl = rtrim(config('services.rakbank.base_url'), '/');
    }

    /**
     * Initiate a Checkout Session with RAKBANK
     */
    public function initiateCheckout($order)
    {
        $url = "{$this->baseUrl}/merchant/{$this->merchantId}/session";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("merchant.{$this->merchantId}:{$this->apiPassword}"),
        ])->post($url, [
            'apiOperation' => 'INITIATE_CHECKOUT',
            'interaction' => [
                'operation' => 'PURCHASE',
                'returnUrl' => route('rakbank.callback', ['order_id' => $order->id]),
                'cancelUrl' => route('checkout.index'),
                'merchant' => [
                    'name' => config('app.name'),
                ]
            ],
            'order' => [
                'id' => $order->invoice_no,
                'amount' => $order->total,
                'currency' => 'AED',
                'description' => 'Online Order ' . $order->invoice_no,
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('RAKBANK Initiate Checkout Failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'order_id' => $order->id
        ]);

        return null;
    }

    /**
     * Retrieve Session Details to verify payment status
     */
    public function retrieveSession($sessionId)
    {
        $url = "{$this->baseUrl}/merchant/{$this->merchantId}/session/{$sessionId}";

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("merchant.{$this->merchantId}:{$this->apiPassword}"),
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('RAKBANK Retrieve Session Failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'session_id' => $sessionId
        ]);

        return null;
    }

    /**
     * Retrieve Order Details from Gateway to confirm transaction
     */
    public function retrieveOrder($orderInvoiceId)
    {
        $url = "{$this->baseUrl}/merchant/{$this->merchantId}/order/{$orderInvoiceId}";

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("merchant.{$this->merchantId}:{$this->apiPassword}"),
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
