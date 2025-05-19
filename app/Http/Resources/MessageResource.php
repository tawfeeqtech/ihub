<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'body' => $this->body,
            'attachment' => $this->attachment ? asset($this->attachment) : null,

            // 'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'sender_id' => $this->sender_id,
            'created_at' => $this->created_at,
        ];
    }
}
