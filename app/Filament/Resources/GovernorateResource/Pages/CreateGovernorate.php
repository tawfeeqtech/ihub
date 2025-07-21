<?php

namespace App\Filament\Resources\GovernorateResource\Pages;

use App\Filament\Resources\GovernorateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Traits\TranslatableFormMutator;

class CreateGovernorate extends CreateRecord
{
    use TranslatableFormMutator {
        mutateFormDataBeforeCreate as translatableMutator;
    }
    protected static string $resource = GovernorateResource::class;
    protected array $translatableFields = [
        'name' => ['value'],
    ];
}
