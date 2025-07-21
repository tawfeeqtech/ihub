<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\TranslatableFormMutator;

class CreateRegion extends CreateRecord
{
    use TranslatableFormMutator {
        mutateFormDataBeforeCreate as translatableMutator;
    }
    protected static string $resource = RegionResource::class;
    protected array $translatableFields = [
        'name' => ['value'],
    ];
}
