<?php

namespace App\Filament\Resources\WorkspaceResource\Pages;

use App\Filament\Resources\WorkspaceResource;
use App\Traits\TranslatableFormMutator;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateWorkspace extends CreateRecord
{
    protected static string $resource = WorkspaceResource::class;
    use TranslatableFormMutator;
    protected array $translatableFields = ['name', 'description'];

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('logo')
                ->label(__('filament.logo'))
                ->image()
                ->disk('public')
                ->directory('temp/logos')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                ->nullable()
                ->preserveFilenames(),

            FileUpload::make('workspace_images')
                ->label(__('filament.workspace_images'))
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('temp/images')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                ->enableReordering()
                ->enableOpen()
                ->enableDownload()
                ->preserveFilenames(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->convertAllTranslatables($data);
        $this->formData = $data;
        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->formData['logo']) && $this->formData['logo']) {
            $newLogoPath = $this->moveFileToWorkspaceDirectory($this->formData['logo'], $this->record->id, 'logos');
            $this->formData['logo'] = $newLogoPath;
            $this->record->update(['logo' => $newLogoPath]);
        }

        if (isset($this->formData['workspace_images']) && is_array($this->formData['workspace_images'])) {
            $newImagePaths = [];
            foreach ($this->formData['workspace_images'] as $image) {
                $newPath = $this->moveFileToWorkspaceDirectory($image, $this->record->id, 'images');
                $newImagePaths[] = $newPath;
                $this->record->images()->create([
                    'image' => $newPath,
                ]);
            }
            $this->formData['workspace_images'] = $newImagePaths;
        }

        $this->cleanUpTempDirectories();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->revertAllTranslatables($data);
        $data['workspace_images'] = $this->record?->images->pluck('image')->toArray() ?? [];
        $data['features'] = $this->record?->features ?? [['ar' => '', 'en' => '']];
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->convertAllTranslatables($data);
        $this->formData = $data;
        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->formData['workspace_images']) && is_array($this->formData['workspace_images'])) {
            $this->record->images()->delete();
            foreach ($this->formData['workspace_images'] as $image) {
                $newPath = $this->moveFileToWorkspaceDirectory($image, $this->record->id, 'images');
                $this->record->images()->create([
                    'image' => $newPath,
                ]);
            }
        }

        $this->cleanUpTempDirectories();
    }

    protected function moveFileToWorkspaceDirectory(string $filePath, int $workspaceId, string $type): string
    {
        $newDirectory = "workspaces/{$workspaceId}/{$type}";
        $fileName = basename($filePath);
        $newPath = "{$newDirectory}/{$fileName}";

        Storage::disk('public')->makeDirectory($newDirectory);

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->move($filePath, $newPath);
        }

        return $newPath;
    }

    protected function cleanUpTempDirectories(): void
    {
        $tempDirs = ['temp/logos', 'temp/images'];
        foreach ($tempDirs as $dir) {
            if (Storage::disk('public')->exists($dir) && empty(Storage::disk('public')->files($dir))) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        }
    }
}