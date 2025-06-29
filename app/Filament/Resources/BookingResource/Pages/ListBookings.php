<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

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
