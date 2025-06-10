<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingNotification;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $userName = $booking->user->name;

        $secretary = User::where('role', 'secretary')->first();

        if ($secretary && $secretary->device_token) {
            $secretary->notify(new NewBookingNotification($userName));
        }
    }
}
