<?php

namespace App\Filament\Widgets;

use App\Models\Workspace;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminWorkspacesTrendChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminWorkspacesTrendChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $workspaces = Workspace::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.AdminWorkspacesTrendChart.heading'),
                    'data' => $workspaces->pluck('count')->toArray(),
                    'backgroundColor' => '#FFCE56',
                    'borderColor' => '#FFCE56',
                ],
            ],
            'labels' => $workspaces->pluck('date')->toArray(),
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
