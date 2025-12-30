<?php

namespace App\Service;

use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        if (!$authResponse->successful()) {
            Log::error('Paymob Auth Failed: ' . $authResponse->body());
            throw new \Exception('Paymob Authentication Failed');
        }

        $token = $authResponse->json('token');

        // 2. Order Registration
        $orderResponse = Http::post('https://egypt.paymob.com/api/ecommerce/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $unit->price * 100,
            'currency' => 'EGP',
            'items' => [],
        ]);

        if (!$orderResponse->successful()) {
            Log::error('Paymob Order Failed: ' . $orderResponse->body());
            throw new \Exception('Paymob Order Registration Failed');
        }

        $orderId = (string) $orderResponse->json('id');

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

        if (!$paymentKeyResponse->successful()) {
            Log::error('Paymob Payment Key Failed: ' . $paymentKeyResponse->body());
            throw new \Exception('Paymob Payment Key Generation Failed');
        }

        $paymentToken = $paymentKeyResponse->json('token');

        // Reserve the unit immediately to prevent double booking
        if ($unit->offer_type === 'sale') {
            $unit->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);
        } elseif ($unit->offer_type === 'rent') {
            $unit->update([
                'status' => 'rented',
                'rented_at' => now(),
            ]);
        }

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
        // Paymob success can be boolean or string 'true'
        $success = false;
        if (isset($data['success'])) {
            $success = ($data['success'] === true || $data['success'] === 'true');
        }

        $transactionRef = $data['order'] ?? null;

        if (!$transactionRef) {
            return false;
        }

        $transaction = Transaction::where('transaction_ref', $transactionRef)->first();

        if ($transaction) {
            $transaction->update([
                'payment_status' => $success ? 'paid' : 'failed',
            ]);

            $unit = $transaction->unit;
            if ($unit) {
                if ($success) {
                    // Status is already set to sold/rented in initiatePayment
                    // We just ensure it's correct here if needed
                    $status = ($unit->offer_type === 'rent' ? 'rented' : 'sold');
                    $timestampField = ($unit->offer_type === 'rent' ? 'rented_at' : 'sold_at');

                    if ($unit->status !== $status) {
                        $unit->update([
                            'status' => $status,
                            $timestampField => now(),
                        ]);
                    }
                } else {
                    // Payment failed or was canceled: revert unit to 'approved'
                    $unit->update([
                        'status' => 'approved',
                        'sold_at' => null,
                        'rented_at' => null,
                    ]);
                }
            }
        }

        return $success;
    }
}
