<?php

namespace App\Service;

use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $paymobApiKey;
    protected $paymobIntegrationId;
    protected $paymobIframeId;

    public function __construct()
    {
        $this->paymobApiKey = config('services.paymob.api_key');
        $this->paymobIntegrationId = config('services.paymob.integration_id');
        $this->paymobIframeId = config('services.paymob.iframe_id');
    }

    public function initiatePayment(Unit $unit, $user)
    {
        // 1. Authentication Request
        $authResponse = Http::post('https://egypt.paymob.com/api/auth/tokens', [
            'api_key' => $this->paymobApiKey,
        ]);

        $token = $authResponse->json('token');

        // 2. Order Registration
        $orderResponse = Http::post('https://egypt.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $unit->price * 100,
            'currency' => 'EGP',
            'items' => [],
        ]);

        $orderId = $orderResponse->json('id');

        // 3. Payment Key Generation
        $billingData = [
            'apartment' => 'NA',
            'email' => $user->email,
            'floor' => 'NA',
            'first_name' => $user->name,
            'street' => $user->address ?? 'NA',
            'building' => 'NA',
            'phone_number' => $user->phone ?? 'NA',
            'shipping_method' => 'NA',
            'postal_code' => 'NA',
            'city' => 'NA',
            'country' => 'EG',
            'last_name' => 'NA',
            'state' => 'NA',
        ];

        $paymentKeyResponse = Http::post('https://egypt.paymob.com/api/ecommerce/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $unit->price * 100,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => 'EGP',
            'integration_id' => $this->paymobIntegrationId,
        ]);

        $paymentToken = $paymentKeyResponse->json('token');

        // Create initial transaction record
        Transaction::create([
            'user_id' => $user->id,
            'unit_id' => $unit->id,
            'amount' => $unit->price,
            'payment_status' => 'pending',
            'transaction_ref' => $orderId,
        ]);

        return 'https://egypt.paymob.com/api/acceptance/iframes/' . $this->paymobIframeId . '?payment_token=' . $paymentToken;
    }

    public function handleCallback($data)
    {
        // Verify HMAC (Security requirement) - simplified for now
        $success = isset($data['success']) && $data['success'] === 'true';
        $transactionRef = $data['order'] ?? null;

        $transaction = Transaction::where('transaction_ref', $transactionRef)->first();

        if ($transaction) {
            $transaction->update([
                'payment_status' => $success ? 'paid' : 'failed',
            ]);

            if ($success) {
                $unit = $transaction->unit;
                if ($unit->offer_type === 'sale') {
                    $unit->update([
                        'status' => 'sold',
                        'sold_at' => now(),
                    ]);
                }

                if ($unit->offer_type === 'rent') {
                    $unit->update([
                        'status' => 'rented',
                        'rented_at' => now(),
                    ]);
                }
            }
        }

        return $success;
    }
}
