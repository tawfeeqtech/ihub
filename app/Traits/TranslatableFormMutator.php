<?php

namespace App\Traits;

trait TranslatableFormMutator
{
    use FormHelper;

    protected function getTranslatableFields(): array
    {
        return property_exists($this, 'translatableFields') ? $this->translatableFields : [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->convertAllTranslatables($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->revertAllTranslatables($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateFormDataBeforeCreate($data);
    }
}
