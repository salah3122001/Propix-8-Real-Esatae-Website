<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Service\TestimonialService;
use App\Http\Resources\TestimonialResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialController extends Controller
{
    use ApiResponse;
    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    /**
     * Public index for active testimonials
     */
    public function index()
    {
        $testimonials = $this->testimonialService->getActiveTestimonials();

        if ($testimonials->isEmpty()) {
            return $this->error(__('api.no_testimonials_yet'), 200);
        }

        return $this->paginated(TestimonialResource::class, $testimonials);
    }

    /**
     * User's own testimonials
     */
    public function myTestimonials(Request $request)
    {
        $testimonials = $this->testimonialService->getUserTestimonials($request->user());

        if ($testimonials->isEmpty()) {
            return $this->error(__('api.not_found'), 200);
        }

        return $this->paginated(TestimonialResource::class, $testimonials);
    }

    public function store(\App\Http\Requests\Api\Testimonial\StoreTestimonialRequest $request)
    {
        $testimonial = $this->testimonialService->submitTestimonial($request->user(), $request->validated());

        return response()->json([
            'status' => true,
            'message' => __('api.testimonial_submitted'),
            'data' => new TestimonialResource($testimonial)
        ], 201);
    }

    public function update(\App\Http\Requests\Api\Testimonial\UpdateTestimonialRequest $request, $id)
    {
        try {
            $testimonial = $this->testimonialService->updateTestimonial($request->user(), $id, $request->validated());

            return response()->json([
                'status' => true,
                'message' => __('api.testimonial_updated'),
                'data' => new TestimonialResource($testimonial)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.testimonial_not_found')
            ], 403);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->testimonialService->deleteTestimonial($request->user(), $id);

            return response()->json([
                'status' => true,
                'message' => __('api.testimonial_deleted')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.testimonial_not_found')
            ], 403);
        }
    }
}
