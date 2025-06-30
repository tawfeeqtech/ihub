<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminConversationsActivityChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminConversationsActivityChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $conversations = Conversation::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.AdminConversationsActivityChart.datasets.label'),
                    'data' => $conversations->pluck('count')->toArray(),
                    'backgroundColor' => '#4BC0C4',
                    'borderColor' => '#4BC0C4',
                ],
            ],
            'labels' => $conversations->pluck('date')->toArray(),
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
