<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewServiceRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $userName;
    protected string $serviceName;

    public function __construct(string $userName, string $serviceName)
    {
        $this->userName = $userName;
        $this->serviceName = $serviceName;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'firebase'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "يوجد طلب خدمة \"{$this->serviceName}\" من {$this->userName}",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "يوجد طلب خدمة \"{$this->serviceName}\" من {$this->userName}",
        ]);
    }

    public function toFirebase($notifiable): array
    {
        return [
            'title' => 'طلب خدمة جديد',
            'body' => "يوجد طلب خدمة \"{$this->serviceName}\" من {$this->userName}",
            'token' => $notifiable->device_token,
        ];
    }
}
