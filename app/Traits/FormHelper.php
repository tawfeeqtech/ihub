<?php

namespace App\Traits;

trait FormHelper
{
    protected function convertAllTranslatables(array $data): array
    {
        // foreach ($this->translatableFields as $field) {
        //     if (isset($data["{$field}_translations"])) {
        //         $data[$field] = collect($data["{$field}_translations"])
        //             ->mapWithKeys(fn($item) => [$item['locale'] => $item['value']])
        //             ->toArray();
        //         unset($data["{$field}_translations"]);
        //     }
        // }

        // return $data;
        foreach ($this->getTranslatableFields() as $field) {
            $translations = [];
            foreach ($data[$field . '_translations'] ?? [] as $item) {
                $translations[$item['locale']] = $item['value'];
            }
            $data[$field] = $translations;
            unset($data[$field . '_translations']);
        }
        return $data;
    }

    protected function revertAllTranslatables(array $data): array
    {
        // foreach ($this->translatableFields as $field) {
        //     if (isset($data[$field])) {
        //         $data["{$field}_translations"] = collect($data[$field])
        //             ->map(fn($value, $locale) => [
        //                 'locale' => $locale,
        //                 'value' => $value,
        //             ])->values()->toArray();
        //     }
        // }

        // return $data;
        foreach ($this->getTranslatableFields() as $field) {
            $translations = [];
            foreach ($data[$field] ?? [] as $locale => $value) {
                $translations[] = [
                    'locale' => $locale,
                    'value' => $value,
                ];
            }
            $data[$field . '_translations'] = $translations;
        }
        return $data;
    }
}
