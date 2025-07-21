<?php

namespace App\Filament\Resources\WorkspaceResource\Pages;

use App\Filament\Resources\WorkspaceResource;
use App\Traits\TranslatableFormMutator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditWorkspace extends EditRecord
{
    protected static string $resource = WorkspaceResource::class;
    use TranslatableFormMutator;
    protected array $translatableFields = ['name',  'description'];
    protected ?string $oldLogo = null;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->revertAllTranslatables($data);

        $data['logo']           = $this->record->logo;
        $data['workspace_images'] = $this->record->images
            ->pluck('image')
            ->filter()
            ->values()
            ->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->oldLogo = $this->record->getOriginal('logo');

        $data = $this->convertAllTranslatables($data);

        if (isset($data['location']) && is_array($data['location'])) {
            $data['location'] = json_encode($data['location'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['logo']) && filter_var($data['logo'], FILTER_VALIDATE_URL)) {
            $data['logo'] = str_replace(Storage::disk('public')->url(''), '', $data['logo']);
        } elseif (!isset($data['logo']) || $data['logo'] === null) {
            $data['logo'] = null;
        }

        if (isset($data['workspace_images']) && is_array($data['workspace_images'])) {
            $data['workspace_images'] = array_map(function ($image) {
                return filter_var($image, FILTER_VALIDATE_URL) ? str_replace(Storage::disk('public')->url(''), '', $image) : $image;
            }, $data['workspace_images']);
        } else {
            $data['workspace_images'] = [];
        }

        Log::info('Form data before save: ', [
            'logo' => $data['logo'] ?? null,
            'workspace_images' => $data['workspace_images'] ?? null,
        ]);
        $this->formData = $data;

        return $data;
    }

    protected function afterSave(): void
    {



        if (array_key_exists('logo', $this->formData)) {
            $newLogo = $this->formData['logo'] ?? null;

            if ($this->oldLogo && ($newLogo !== $this->oldLogo || is_null($newLogo))) {
                if (Storage::disk('public')->exists($this->oldLogo)) {
                    Storage::disk('public')->delete($this->oldLogo);
                    Log::info('Deleted old logo from storage: ', ['old_logo' => $this->oldLogo]);
                }
            }
        }

        // if (array_key_exists('workspace_images', $this->formData)) {
        //     $existingImages = $this->record->images->pluck('image')->toArray();
        //     $newImages = $this->formData['workspace_images'] ?? [];
        //     foreach ($existingImages as $existingImage) {
        //         if (!in_array($existingImage, $newImages)) {
        //             if (Storage::disk('public')->exists($existingImage)) {
        //                 Storage::disk('public')->delete($existingImage);
        //             }
        //         }
        //     }
        //     $this->record->images()->delete();
        //     foreach ($newImages as $image) {
        //         $this->record->images()->create([
        //             'image' => $image,
        //         ]);
        //     }
        // }
        if (array_key_exists('workspace_images', $this->formData)) {
            $existingImages = $this->record->images()->pluck('image', 'id')->toArray(); // [id => image_path]
            $newImages = $this->formData['workspace_images'] ?? [];

            // الصور المحذوفة: موجودة في DB وليست في الجديد
            $imagesToDelete = array_diff($existingImages, $newImages);

            // الصور الجديدة: موجودة في الجديد وليست في DB
            $imagesToAdd = array_diff($newImages, $existingImages);

            // حذف الصور المحذوفة من التخزين والـ DB
            foreach ($imagesToDelete as $id => $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
                $this->record->images()->where('id', $id)->delete();
            }

            // إضافة الصور الجديدة فقط
            foreach ($imagesToAdd as $image) {
                $this->record->images()->create([
                    'image' => $image,
                ]);
            }
        }
    }
}
