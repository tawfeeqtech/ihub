<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SecretaryServicesOverTimeChart extends ChartWidget
{
    protected static ?string $heading = 'Services Over Time';
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

        $services = Service::where('workspace_id', $workspaceId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'New Services',
                    'data' => $services->pluck('count')->toArray(),
                    'backgroundColor' => '#4BC0C4',
                    'borderColor' => '#4BC0C4',
                ],
            ],
            'labels' => $services->pluck('date')->toArray(),
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
