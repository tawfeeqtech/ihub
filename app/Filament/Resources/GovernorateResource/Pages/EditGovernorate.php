<?php

namespace App\Filament\Resources\GovernorateResource\Pages;

use App\Filament\Resources\GovernorateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Traits\TranslatableFormMutator;

class EditGovernorate extends EditRecord
{
    use TranslatableFormMutator {
        mutateFormDataBeforeCreate as translatableMutator;
    }
    protected static string $resource = GovernorateResource::class;
    protected array $translatableFields = ['name'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->translatableMutator($data);
    }
}
