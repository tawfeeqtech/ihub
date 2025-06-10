<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewBookingNotification extends Notification implements ShouldQueue
{

    use Queueable;

    protected string $userName;

    public function __construct(string $userName)
    {
        $this->userName = $userName;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'firebase'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "يوجد طلب \"حجز مساحة عمل\" جديد من {$this->userName}",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "يوجد طلب \"حجز مساحة عمل\" جديد من {$this->userName}",
        ]);
    }

    public function toFirebase($notifiable): array
    {
        return [
            'title' => 'طلب حجز جديد',
            'body' => "يوجد طلب \"حجز مساحة عمل\" جديد من {$this->userName}",
            'token' => $notifiable->device_token,
        ];
    }
}
