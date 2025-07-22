<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class WorkspaceShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = App::getLocale();
        return [
            'id' => $this->id,
            'name' => $this->name[$lang] ?? $this->name['en'] ?? '',
            'location' => $this->location[$lang] ?? $this->location['en'] ?? '',
            'description' => $this->description[$lang] ?? $this->description['en'] ?? '',
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'images' => WorkspaceImageResource::collection($this->whenLoaded('images')),
            'features' => collect($this->features ?? [])->map(fn($feature) => $feature[$lang] ?? $feature['en'] ?? '')->filter()->values()->all(),
            'governorate' => [
                'id' => $this->governorate_id,
                'name' => $this->governorate ? $this->governorate->getTranslatedNameAttribute($lang) : '',
            ],
            'region' => [
                'id' => $this->region_id,
                'name' => $this->region ? $this->region->getTranslatedNameAttribute($lang) : '',
            ],
            'short_services' => $this->services
                ->pluck('category')
                ->unique(fn($item) => $item[$lang] ?? $item['en'] ?? $item['ar'] ?? null)
                ->map(fn($item) => $item[$lang] ?? $item['en'] ?? $item['ar'] ?? '')
                ->values(),
            'secretary' => $this->secretary ? [
                'id' => $this->secretary->id,
                'name' => $this->secretary->name,
                'phone' => $this->secretary->phone,
            ] : null,
        ];
    }
}
