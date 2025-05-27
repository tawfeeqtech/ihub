<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Traits\TranslatableFormMutator;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use TranslatableFormMutator {
        mutateFormDataBeforeCreate as translatableMutator;
    }

    protected static string $resource = ServiceResource::class;
    protected array $translatableFields = [
        'name' => ['value'],
        'category' => ['custom', 'predefined', 'value'], // ترتيب الأفضلية
    ];



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->translatableMutator($data);
        $data['workspace_id'] = auth()->user()->workspace_id;
        return $data;
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['workspace_id'] = auth()->user()->workspace_id;
    //     dd($data);
    //     return $data;
    // }
}
