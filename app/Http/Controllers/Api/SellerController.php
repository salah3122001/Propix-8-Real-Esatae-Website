<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\FetchSellersRequest;
use App\Http\Resources\SellerResource;
use App\Service\SellerService;

class SellerController extends Controller
{
    protected $sellerService;

    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    public function index(FetchSellersRequest $request)
    {
        $sellers = $this->sellerService->getAllSellers();
        return response()->json([
            'success' => true,
            'data' => SellerResource::collection($sellers),
        ]);
    }

    public function show($id)
    {
        $seller = $this->sellerService->getSellerById($id);

        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => __('api.seller_not_found'), // Ensure this key exists or add it
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SellerResource($seller),
        ]);
    }
}
