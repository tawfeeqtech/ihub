<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'secretary_id' => $this->secretary_id,
            'last_message' => $this->messages()->latest()->first()?->body,
            'last_message_at' => $this->messages()->latest()->first()?->created_at,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
