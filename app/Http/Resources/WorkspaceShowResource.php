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
            'images' => WorkspaceImageResource::collection($this->whenLoaded('images')), // <--- استخدمه هنا أيضاً
            'features' => $this->features ?? [],
            'short_services' => $this->services
                ->pluck('category')
                ->unique()
                ->values(),
            'secretary' => $this->secretary ? [
                'id' => $this->secretary->id,
                'name' => $this->secretary->name,
                'phone' => $this->secretary->phone,
            ] : null,
        ];
    }
}
