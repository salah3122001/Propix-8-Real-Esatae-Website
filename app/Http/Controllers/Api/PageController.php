<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Service\PageService;

class PageController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {
        $pages = $this->pageService->getAll();
        if ($pages->isEmpty()) {
            return $this->error(__('api.no_pages_yet'), 200);
        }
        return PageResource::collection($pages);
    }

    public function show($slug)
    {
        $page = $this->pageService->getBySlug($slug);
        return new PageResource($page);
    }
}
