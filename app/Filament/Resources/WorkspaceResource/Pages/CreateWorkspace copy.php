<?php

namespace App\Filament\Resources\WorkspaceResource\Pages;

use App\Filament\Resources\WorkspaceResource;
use App\Traits\TranslatableFormMutator;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Support\Facades\Log;

class CreateWorkspace extends CreateRecord
{
    protected static string $resource = WorkspaceResource::class;
    use TranslatableFormMutator;
    protected array $translatableFields = ['name',  'description'];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->convertAllTranslatables($data);
        Log::info('Form data before create: ', [
            'logo' => $data['logo'] ?? null,
            'workspace_images' => $data['workspace_images'] ?? null,
        ]);

        $this->formData = $data;

        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->formData['workspace_images']) && is_array($this->formData['workspace_images'])) {
            foreach ($this->formData['workspace_images'] as $image) {
                $this->record->images()->create([
                    'image' => $image,
                ]);
            }
        }
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->revertAllTranslatables($data);
        $data['workspace_images'] = $this->record?->images->pluck('image')->toArray() ?? [];
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->convertAllTranslatables($data);
        Log::info('Form data before save: ', [
            'logo' => $data['logo'] ?? null,
            'workspace_images' => $data['workspace_images'] ?? null,
        ]);
        $this->formData = $data;
        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->formData['workspace_images']) && is_array($this->formData['workspace_images'])) {
            $this->record->images()->delete();
            foreach ($this->formData['workspace_images'] as $image) {
                $this->record->images()->create([
                    'image' => $image,
                ]);
            }
        }
    }
}
