<?php

namespace App\Notifications;

use App\ServiceRequestsStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceRequest;

class ServiceRequestStatusUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ServiceRequest $request) {}


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'تم تحديث حالة طلبك إلى: ' . $this->translateStatus($this->request->status),
            'request_id' => $this->request->id,
        ];
    }

    protected function translateStatus($status): string
    {
        if ($status instanceof ServiceRequestsStatus) {
            return $status->label();
        }

        // fallback للأنواع الأخرى
        return (string) $status;
    }
}
