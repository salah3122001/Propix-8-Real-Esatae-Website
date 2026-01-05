<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_path' => asset('storage/app/public/' . $this->url) ?? '',
            'hls_path' => $this->processed_url ? asset('storage/app/public/' . $this->processed_url) : '',
            'processing_status' => $this->processing_status ?? 'pending',
            'type' => $this->type ?? 'image',
        ];
    }
}
