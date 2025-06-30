<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminBookingsChart extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminBookingsChart.heading');
    }

    protected function getData(): array
    {
        $bookings = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.Bookings.datasets.label'),
                    'data' => $bookings->pluck('count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
            ],
            'labels' => $bookings->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('admin');
    }
}
