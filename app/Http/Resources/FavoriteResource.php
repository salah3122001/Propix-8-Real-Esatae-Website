<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unit' => [
                'id' => $this->unit_id,
                'title' => $this->unit->{'title_' . app()->getLocale()} ?? $this->unit->title_ar,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
