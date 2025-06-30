<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SecretaryServiceRequestsByStatusChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.SecretaryServiceRequestsByStatusChart.heading');
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

        $serviceRequests = ServiceRequest::forWorkspace($workspaceId)
            ->select('status')
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.SecretaryServiceRequestsByStatusChart.datasets.label'),
                    'data' => $serviceRequests->pluck('count')->toArray(),
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56'],
                ],
            ],
            'labels' => $serviceRequests->pluck('status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('secretary') && Auth::user()->workspace;
    }
}
