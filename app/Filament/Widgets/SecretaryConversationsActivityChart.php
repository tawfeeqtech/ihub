<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SecretaryConversationsActivityChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.SecretaryConversationsActivityChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $userId = Auth::user()->id ?? null;

        if (!$userId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $conversations = Conversation::where('secretary_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.SecretaryConversationsActivityChart.heading'),
                    'data' => $conversations->pluck('count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
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
        return Auth::user() && Auth::user()->hasRole('secretary') && Auth::user()->workspace;
    }
}
