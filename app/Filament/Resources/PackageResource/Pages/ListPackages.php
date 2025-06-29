<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        if ($user->role === 'secretary') {
            return $this->getModel()::query()->where('workspace_id', $user->workspace_id);
        }

        // للمشرف، امنعه من رؤية أي بيانات (أو قم بتعديل ذلك حسب ما تريد)
        return $this->getModel()::query()->whereRaw('0 = 1');
    }
}
