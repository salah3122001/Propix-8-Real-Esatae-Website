<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Service\GeneralServiceService;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(GeneralServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        $services = $this->serviceService->getAll();
        if ($services->isEmpty()) {
            return $this->error(__('api.no_services_yet'), 200);
        }
        return ServiceResource::collection($services);
    }

    public function show($id)
    {
        $service = $this->serviceService->getById($id);
        return new ServiceResource($service);
    }
}
