<?php

namespace App\Notifications;



use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;


class UnreadMessagesNotificationCopy extends Notification
{
    protected $userName;
    protected $unreadCount;

    public function __construct(string $userName, int $unreadCount)
    {
        $this->userName = $userName;
        $this->unreadCount = $unreadCount;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'firebase']; // 'firebase' حسب تكاملك مع FCM
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "لديك {$this->unreadCount} رسالة/رسائل غير مقروءة من {$this->userName}",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "لديك {$this->unreadCount} رسالة/رسائل غير مقروءة من {$this->userName}",
        ]);
    }

    // مثال لإرسال عبر Firebase (حسب إعداداتك)
    public function toFirebase($notifiable)
    {
        return [
            'title' => 'رسائل جديدة',
            'body' => "لديك {$this->unreadCount} رسالة/رسائل غير مقروءة من {$this->userName}",
            'token' => $notifiable->device_token,
        ];
    }
}
