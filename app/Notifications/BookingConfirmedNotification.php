<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'message' => 'تم تأكيد حجزك لمساحة العمل: ' . $this->booking->workspace->name,
            'booking_id' => $this->booking->id,
        ];
    }
}
