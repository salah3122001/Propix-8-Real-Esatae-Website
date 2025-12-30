<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Service\FaqService;

class FaqController extends Controller
{
    protected $faqService;

    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    public function index()
    {
        $faqs = $this->faqService->getAll();
        if ($faqs->isEmpty()) {
            return $this->error(__('api.no_faqs_yet'), 200);
        }
        return FaqResource::collection($faqs);
    }
}
