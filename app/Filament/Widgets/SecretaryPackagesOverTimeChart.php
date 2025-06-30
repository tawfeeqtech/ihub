<?php

namespace App\Filament\Widgets;

use App\Models\Package;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SecretaryPackagesOverTimeChart extends ChartWidget
{
    protected static ?string $heading = 'Packages Over Time';
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

        $packages = Package::where('workspace_id', $workspaceId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'New Packages',
                    'data' => $packages->pluck('count')->toArray(),
                    'backgroundColor' => '#FFCE56',
                    'borderColor' => '#FFCE56',
                ],
            ],
            'labels' => $packages->pluck('date')->toArray(),
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
