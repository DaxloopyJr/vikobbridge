<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Group;

class SelcomPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $vendorId;

    public function __construct()
    {
        $this->baseUrl = config('selcom.base_url', 'https://api.selcommobile.com');
        $this->apiKey = config('selcom.api_key');
        $this->apiSecret = config('selcom.api_secret');
        $this->vendorId = config('selcom.vendor_id');
    }

    protected function generateAuthorization(string $timestamp): string
    {
        $dataToSign = $this->apiKey . $timestamp;
        $signature = hash_hmac('sha256', $dataToSign, $this->apiSecret);
        return 'SELCOM ' . base64_encode($this->apiKey . ':' . $signature);
    }

    protected function getHeaders(): array
    {
        $timestamp = now()->toIso8601String();
        return [
            'Authorization' => $this->generateAuthorization($timestamp),
            'X-Timestamp' => $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function createPaymentOrder(array $data): array
    {
        try {
            $payload = [
                'vendor' => $this->vendorId,
                'order_id' => $data['order_id'],
                'buyer_email' => $data['email'] ?? '',
                'buyer_name' => $data['name'] ?? '',
                'buyer_phone' => $data['phone'] ?? '',
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'TZS',
                'buyer_refercode' => '',
                'webhook' => config('selcom.callback_url'),
                'return_url' => $data['return_url'] ?? config('app.url') . '/payment/success',
                'cancel_url' => $data['cancel_url'] ?? config('app.url') . '/payment/cancel',
                'no_of_items' => 1,
                'partner_name' => config('app.name'),
                'partner_orderid' => $data['order_id'],
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/v1/checkout/create-order-minimal", $payload);

            $result = $response->json();

            Log::info('Selcom payment order created', [
                'order_id' => $data['order_id'],
                'response' => $result,
            ]);

            return [
                'success' => $response->successful() && ($result['result'] ?? '') === 'SUCCESS',
                'data' => $result,
                'payment_url' => $result['data']['payment_gateway_url'] ?? null,
                'order_id' => $result['data']['order_id'] ?? $data['order_id'],
            ];
        } catch (\Exception $e) {
            Log::error('Selcom payment order creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage(),
            ];
        }
    }

    public function checkPaymentStatus(string $orderId): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/v1/checkout/order-status", [
                    'order_id' => $orderId,
                    'vendor' => $this->vendorId,
                ]);

            $result = $response->json();

            return [
                'success' => $response->successful(),
                'status' => $result['data']['payment_status'] ?? 'unknown',
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Selcom payment status check failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage(),
            ];
        }
    }

    public function processCallback(array $data): bool
    {
        try {
            $orderId = $data['order_id'] ?? null;
            $paymentStatus = $data['payment_status'] ?? null;

            if (!$orderId) {
                Log::warning('Selcom callback missing order_id', $data);
                return false;
            }

            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found for callback', ['order_id' => $orderId]);
                return false;
            }

            if ($paymentStatus === 'COMPLETED') {
                $payment->markAsCompleted();
            } elseif (in_array($paymentStatus, ['FAILED', 'CANCELLED'])) {
                $payment->markAsFailed($data['message'] ?? 'Payment ' . strtolower($paymentStatus));
            }

            $payment->update([
                'payment_response' => array_merge($payment->payment_response ?? [], $data),
            ]);

            Log::info('Selcom callback processed', [
                'order_id' => $orderId,
                'status' => $paymentStatus,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Selcom callback processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }

    public function initiateDirectPayment(array $data): array
    {
        try {
            $payload = [
                'vendor' => $this->vendorId,
                'order_id' => $data['order_id'],
                'buyer_email' => $data['email'] ?? '',
                'buyer_name' => $data['name'] ?? '',
                'buyer_phone' => $data['phone'] ?? '',
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'TZS',
                'payment_phone' => $data['payment_phone'],
                'payment_operator' => $data['payment_operator'] ?? 'TIGOPESA',
                'webhook' => config('selcom.callback_url'),
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/v1/direct/deposit", $payload);

            $result = $response->json();

            return [
                'success' => $response->successful() && ($result['result'] ?? '') === 'SUCCESS',
                'data' => $result,
                'message' => $result['message'] ?? 'Payment initiated',
            ];
        } catch (\Exception $e) {
            Log::error('Selcom direct payment failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }

    public function generateControlNumber(array $data): array
    {
        try {
            $payload = [
                'vendor' => $this->vendorId,
                'order_id' => $data['order_id'],
                'buyer_email' => $data['email'] ?? '',
                'buyer_name' => $data['name'] ?? '',
                'buyer_phone' => $data['phone'] ?? '',
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'TZS',
                'buyer_refercode' => '',
                'webhook' => config('selcom.callback_url'),
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/v1/checkout/generate-control-number", $payload);

            $result = $response->json();

            return [
                'success' => $response->successful() && ($result['result'] ?? '') === 'SUCCESS',
                'control_number' => $result['data']['control_number'] ?? null,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Selcom control number generation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to generate control number: ' . $e->getMessage(),
            ];
        }
    }
}
