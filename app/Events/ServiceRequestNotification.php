<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $recipient;
    public $message;
    public $isSecretary;

    public function __construct(User $recipient, string $message, bool $isSecretary)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->isSecretary = $isSecretary;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->recipient->id);
    }

    public function broadcastAs()
    {
        return 'service-request-notification';
    }
}
