<?php

namespace App\Filament\Widgets;

use App\BookingStatus;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminBookingsByStatusChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminBookingsByStatusChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $bookings = Booking::select('status')
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.Bookings.datasets.label'),
                    'data' => $bookings->pluck('count')->toArray(),
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56'],
                ],
            ],
            'labels' => $bookings
                ->pluck('status')
                ->unique()
                ->map(function (BookingStatus $status) {
                    return $status->label();
                })
                ->toArray()


        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('admin');
    }
}
