<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();

        return [
            "id" => $this->id,
            "title" => ($lang === "ar" ? $this->title_ar : $this->title_en) ?? "",
            "address" => $this->address ?? "",
            "price" => $this->price ?? 0,
            "offer_type" => $this->offer_type ?? "",
            "area" => $this->area ?? 0,
            "rooms" => $this->rooms ?? 0,
            "bathrooms" => $this->bathrooms ?? 0,
            "city" => [
                "id" => $this->city_id,
                "name" => ($lang === "ar" ? ($this->city->name_ar ?? "") : ($this->city->name_en ?? "")),
            ],
            "unit_type" => [
                "id" => $this->unit_type_id,
                "name" => ($lang === "ar" ? ($this->type->name_ar ?? "") : ($this->type->name_en ?? "")),
                "icon" => $this->type->icon ? asset("storage/app/public/" . $this->type->icon) : "",
            ],
            "main_image" => $this->whenLoaded("media", function () {
                $image = $this->media->where("type", "image")->first();
                return $image ? asset("storage/app/public/" . $image->url) : "";
            }),
            "created_at" => $this->created_at?->toISOString() ?? "",
        ];
    }
}
