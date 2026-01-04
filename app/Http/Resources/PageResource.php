<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $lang === 'ar' ? $this->title_ar : $this->title_en,
            'content' => $lang === 'ar' ? $this->content_ar : $this->content_en,
            'team_members' => collect($this->team_members)->map(fn ($member) => [
                'name' => $member['name'] ?? '',
                'position' => $member['position'] ?? '',
                'photo' => isset($member['photo']) ? asset('public/storage/' . $member['photo']) : null,
            ]),
        ];
    }
}
