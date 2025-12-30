<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Service\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        if ($unit->status === 'sold') {
            return response()->json([
                'success' => false,
                'message' => __('api.unit_already_sold')
            ], 403);
        }

        if ($unit->status === 'rented') {
            return response()->json([
                'success' => false,
                'message' => __('api.unit_already_rented')
            ], 403);
        }

        if ($unit->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => __('api.unit_not_approved')
            ], 403);
        }


        $user = $request->user();

        $paymentUrl = $this->paymentService->initiatePayment($unit, $user);

        return response()->json([
            'payment_url' => $paymentUrl,
        ]);
    }

    public function callback(Request $request)
    {
        // Paymob callback logic
        $data = $request->all();
        $success = $this->paymentService->handleCallback($data);

        return response()->json([
            'success' => $success,
        ]);
    }
}