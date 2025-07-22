<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale(); // Get language from Accept-Language header

        return [
            'id' => $this->id,
            'name' => $this->getTranslatedNameAttribute($lang),
            'regions' => $this->regions->map(function ($region) use ($lang) {
                return [
                    'id' => $region->id,
                    'name' => $region->getTranslatedNameAttribute($lang),
                ];
            })->toArray(),
        ];
    }
}