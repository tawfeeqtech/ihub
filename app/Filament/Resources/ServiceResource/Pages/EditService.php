<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Traits\TranslatableFormMutator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use TranslatableFormMutator {
        mutateFormDataBeforeCreate as translatableMutator;
    }

    protected static string $resource = ServiceResource::class;

    protected array $translatableFields = ['name', 'category' => ['value', 'predefined']];


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->translatableMutator($data);
        $data['workspace_id'] = auth()->user()->workspace_id;
        return $data;
    }
}
