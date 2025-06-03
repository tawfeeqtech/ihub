<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    use InteractsWithSockets;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn(): object
    {
        return new PrivateChannel('conversations.' . $this->message->conversation_id);

        // return [
        //     new PrivateChannel('conversations.' . $this->message->conversation_id),
        //     new PrivateChannel('secretary.' . $this->message->conversation->secretary_id),
        // ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            // 'conversation_id' => $this->message->conversation_id,
            'body' => $this->message->body,
            'sender_id' => $this->message->sender_id,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
            ],
            'created_at' => $this->message->created_at->toDateTimeString(),
            'attachment' => $this->message->attachment ?? null,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
