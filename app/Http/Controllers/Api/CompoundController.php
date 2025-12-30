<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompoundResource;
use App\Service\CompoundService;

class CompoundController extends Controller
{
    protected $compoundService;

    public function __construct(CompoundService $compoundService)
    {
        $this->compoundService = $compoundService;
    }

    public function index()
    {
        $compounds = $this->compoundService->getAllCompounds();
        if ($compounds->isEmpty()) {
            return $this->error(__('api.no_compounds_yet'), 200);
        }
        return $this->success(CompoundResource::collection($compounds));
    }

    public function show($id)
    {
        $compound = $this->compoundService->getCompoundById($id);
        return $this->success(new CompoundResource($compound));
    }
}
