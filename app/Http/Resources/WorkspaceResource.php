<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class WorkspaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = App::getLocale(); // اللغة المفعّلة من الهيدر

        return [
            'id' => $this->id,
            'name' => $this->name[$lang] ?? $this->name['en'] ?? '',
            'location' => $this->location[$lang] ?? $this->location['en'] ?? '',
            'description' => $this->description[$lang] ?? $this->description['en'] ?? '',
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'bank_payment_supported' => $this->bank_payment_supported,
            'has_evening_shift' => $this->has_evening_shift,
            'has_free' => $this->has_free,
            'phone' => $this->phone,
            'governorate' => [
                'id' => $this->governorate_id,
                'name' => $this->governorate ? $this->governorate->getTranslatedNameAttribute($lang) : '',
            ],
            'region' => [
                'id' => $this->region_id,
                'name' => $this->region ? $this->region->getTranslatedNameAttribute($lang) : '',
            ],
            // 'images' => WorkspaceImageResource::collection($this->whenLoaded('images')), // <--- استخدمه هنا
        ];
    }
}
