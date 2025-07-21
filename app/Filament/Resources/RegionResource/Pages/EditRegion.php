<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\TranslatableFormMutator;

class EditRegion extends EditRecord
{
    use TranslatableFormMutator;
    protected array $translatableFields = ['name'];
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     return $this->translatableMutator($data);
    // }
}
