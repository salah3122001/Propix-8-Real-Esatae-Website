<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Developer\FetchDevelopersRequest;
use App\Http\Resources\DeveloperResource;
use App\Service\DeveloperService;
use App\Traits\ApiResponse;

class DeveloperController extends Controller
{
    use ApiResponse;

    protected $developerService;

    public function __construct(DeveloperService $developerService)
    {
        $this->developerService = $developerService;
    }

    public function index(FetchDevelopersRequest $request)
    {
        $developers = $this->developerService->getAllDevelopers();
        return $this->success(DeveloperResource::collection($developers));
    }

    public function show($id)
    {
        $developer = $this->developerService->getDeveloperById($id);

        if (!$developer) {
            return $this->error(__('api.developer_not_found'), 404);
        }

        return $this->success(new DeveloperResource($developer));
    }
}
