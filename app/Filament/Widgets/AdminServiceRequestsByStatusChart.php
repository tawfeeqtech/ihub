<?php

namespace App\Filament\Widgets;

use App\Models\ServiceRequest;
use App\ServiceRequestsStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminServiceRequestsByStatusChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminServiceRequestsByStatusChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $serviceRequests = ServiceRequest::select('status')
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.AdminServiceRequestsByStatusChart.datasets.label'),
                    'data' => $serviceRequests->pluck('count')->toArray(),
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56'],
                ],
            ],
            'labels' => $serviceRequests
                ->pluck('status')
                ->unique()
                ->map(function (ServiceRequestsStatus $status) {
                    return $status->label();
                })
                ->toArray(),

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
