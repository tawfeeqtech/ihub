<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SecretaryBookingsChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.SecretaryBookingsChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $workspaceId = Auth::user()->workspace->id ?? null;

        if (!$workspaceId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $bookings = Booking::where('workspace_id', $workspaceId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.SecretaryBookingsChart.datasets.label'),
                    'data' => $bookings->pluck('count')->toArray(),
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
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
        return Auth::user() && Auth::user()->hasRole('secretary') && Auth::user()->workspace;
    }
}
