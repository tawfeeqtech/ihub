<?php

namespace App\Filament\Traits;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

trait WorkspaceNameTrait
{
    /**
     * Get the workspace name in the specified locale.
     *
     * @param mixed $model The model with a workspace relationship (e.g., Package, Service)
     * @param string|null $locale The locale to use (defaults to user's locale)
     * @return string
     */
    protected function getWorkspaceName($model, ?string $locale = null): string
    {
        $workspace = $model->workspace;

        if (!$workspace instanceof Workspace) {
            return 'Unknown Workspace (ID: ' . ($model->workspace_id ?? 'N/A') . ')';
        }

        // Use provided locale or fall back to user's locale or app default
        $locale = $locale ?? Auth::user()->current_locale ?? config('app.locale', 'en');

        $name = $workspace->name;

        // Handle JSON name field
        if (is_array($name) || is_object($name)) {
            return $name[$locale] ?? $name['en'] ?? 'Unknown Workspace (ID: ' . $model->workspace_id . ')';
        }

        // Handle translatable package (e.g., spatie/laravel-translatable)
        if (method_exists($workspace, 'getTranslation')) {
            return $workspace->getTranslation('name', $locale, false) ?? $workspace->getTranslation('name', 'en', false) ?? 'Unknown Workspace (ID: ' . $model->workspace_id . ')';
        }

        // Handle plain string name
        return $name ?? 'Unknown Workspace (ID: ' . $model->workspace_id . ')';
    }
}
