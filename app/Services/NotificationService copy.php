<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\NewBookingNotification;

class NotificationService
{
    public function notifySecretaryOfNewBooking(string $userName, int $workspaceId): void
    {
        $secretary = User::where('role', 'secretary')
            ->where('workspace_id', $workspaceId)
            ->first();

        if ($secretary && $secretary->device_token) {
            $secretary->notify(new NewBookingNotification($userName));
        }
    }
}
