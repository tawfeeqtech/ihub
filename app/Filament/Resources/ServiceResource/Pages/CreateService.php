<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['workspace_id'] = auth()->user()->workspace_id;
        return $data;
    }
}
