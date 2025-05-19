<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'images' => $this->images->map(function ($img) {
                return asset($img->image); // إذا كان في public/uploads
            }),
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
