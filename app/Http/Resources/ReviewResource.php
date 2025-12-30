<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name,
            ],
            // Only include unit if it's loaded (useful for getUserReviews)
            'unit' => $this->whenLoaded('unit', function () {
                $lang = app()->getLocale();
                return [
                    'id' => $this->unit->id,
                    'title' => $lang === 'ar' ? $this->unit->title_ar : $this->unit->title_en, // title accessor handles localization
                ];
            }),
        ];
    }
}
