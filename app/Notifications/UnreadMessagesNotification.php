<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class UnreadMessagesNotification extends Notification
{
    use Queueable;

    protected int $unreadCount;
    protected string $userName;
    protected string $secretaryName;

    public function __construct(int $unreadCount, string $userName, string $secretaryName)
    {
        $this->unreadCount = $unreadCount;
        $this->userName = $userName;
        $this->secretaryName = $secretaryName;
    }

    // Add 'database' channel here
    public function via($notifiable)
    {
        return [OneSignalChannel::class, 'database'];
    }

    public function toOneSignal($notifiable)
    {
        if ($notifiable->isSecretary()) {
            return OneSignalMessage::create()
                ->subject("You have {$this->unreadCount} unread message(s) from {$this->userName}")
                ->body("Please check your control panel messages.");
        } else {
            return OneSignalMessage::create()
                ->subject("You have {$this->unreadCount} unread message(s) from {$this->secretaryName}")
                ->body("Please check your app messages.");
        }
    }

    // Store notification data in database
    public function toDatabase($notifiable)
    {
        return [
            'message' => "You have {$this->unreadCount} unread message(s) from " . ($notifiable->isSecretary() ? $this->userName : $this->secretaryName),
            'unread_count' => $this->unreadCount,
            'sender' => $notifiable->isSecretary() ? $this->userName : $this->secretaryName,
            'type' => 'unread_message',
        ];
    }
}
