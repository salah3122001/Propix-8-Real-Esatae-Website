<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Developer\FetchDevelopersRequest;
use App\Http\Resources\DeveloperResource;
use App\Service\DeveloperService;

class DeveloperController extends Controller
{
    protected $developerService;

    public function __construct(DeveloperService $developerService)
    {
        $this->developerService = $developerService;
    }

    public function index(FetchDevelopersRequest $request)
    {
        $developers = $this->developerService->getAllDevelopers();
        return response()->json([
            'success' => true,
            'data' => DeveloperResource::collection($developers),
        ]);
    }

    public function show($id)
    {
        $developer = $this->developerService->getDeveloperById($id);

        if (!$developer) {
            return response()->json([
                'success' => false,
                'message' => __('api.developer_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DeveloperResource($developer),
        ]);
    }
}
