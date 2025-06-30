<?php

namespace App;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __('filament.Widgets.AdminBookingsByStatusChart.labels.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',    // أصفر
            self::Confirmed => 'success',  // أخضر
            self::Cancelled => 'danger',   // أحمر
        };
    }
}
