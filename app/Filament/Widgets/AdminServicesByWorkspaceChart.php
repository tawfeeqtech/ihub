<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\WorkspaceNameTrait;
use App\Models\Service;
use App\Models\Workspace;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminServicesByWorkspaceChart extends ChartWidget
{
    use WorkspaceNameTrait;

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminServicesByWorkspaceChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Get the user's current locale
        $locale = Auth::user()->current_locale ?? config('app.locale', 'en');

        // Fetch services grouped by workspace_id, ensuring valid workspaces
        $services = Service::whereNotNull('workspace_id')
            ->whereHas('workspace')
            ->groupBy('workspace_id')
            ->selectRaw('workspace_id, COUNT(*) as count')
            ->with('workspace')
            ->get();

        // Log::info('AdminServicesByWorkspaceChart Data', [
        //     'services' => $services->map(fn($service) => [
        //         'workspace_id' => $service->workspace_id,
        //         'workspace_name' => $this->getWorkspaceName($service, $locale),
        //         'count' => $service->count,
        //     ])->toArray(),
        // ]);

        // Map to chart data, ensuring valid labels
        $data = $services->map(function ($service) use ($locale) {
            return [
                'workspace_id' => $service->workspace_id,
                'name' => $this->getWorkspaceName($service, $locale),
                'count' => $service->count,
            ];
        })->filter(function ($item) {
            return !is_null($item['name']) && !is_null($item['count']);
        });

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.AdminServicesByWorkspaceChart.datasets.label'),
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#4BC0C4',
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
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
