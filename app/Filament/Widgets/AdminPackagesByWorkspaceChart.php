<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\WorkspaceNameTrait;
use App\Models\Package;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminPackagesByWorkspaceChart extends ChartWidget
{
    use WorkspaceNameTrait;

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.Widgets.AdminPackagesByWorkspaceChart.heading');
    }
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Get the user's current locale
        $locale = Auth::user()->current_locale ?? config('app.locale', 'en');

        // Fetch packages grouped by workspace_id, ensuring valid workspaces
        $packages = Package::whereNotNull('workspace_id')
            ->whereHas('workspace')
            ->groupBy('workspace_id')
            ->selectRaw('workspace_id, COUNT(*) as count')
            ->with('workspace')
            ->get();

        // Map to chart data, ensuring valid labels
        $data = $packages->map(function ($package) use ($locale) {
            return [
                'workspace_id' => $package->workspace_id,
                'name' => $this->getWorkspaceName($package, $locale),
                'count' => $package->count,
            ];
        })->filter(function ($item) {
            return !is_null($item['name']) && !is_null($item['count']);
        });

        return [
            'datasets' => [
                [
                    'label' => __('filament.Widgets.AdminPackagesByWorkspaceChart.datasets.label'),
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#FF6384',
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
