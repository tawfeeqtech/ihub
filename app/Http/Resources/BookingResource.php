<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            // 'user' => [
            //     'id' => $this->user->id,
            //     'name' => $this->user->name,
            //     'phone' => $this->user->phone,
            // ],
            // 'workspace' => new WorkspaceResource($this->workspace),
            // 'package' => new PackageResource($this->package),
            // 'payment_method' => $this->payment_method,
            // 'payment_reference' => $this->payment_reference,
            // 'start_at' => $this->start_at,
            // 'end_at' => $this->end_at,
            // 'status' => $this->status,
            'id' => $this->id,
            'workspace_name' => $this->workspace->name ?? null,
            'package_name' => $this->package->name ?? null,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'seat_number' => $this->seat_number,
            'wifi_username' => $this->wifi_username,
            'wifi_password' => $this->wifi_password,
            'remaining_time' => now()->diffInMinutes($this->end_at, false) > 0
                ? now()->diffForHumans($this->end_at, true)
                : 'منتهي',
        ];
    }
}
