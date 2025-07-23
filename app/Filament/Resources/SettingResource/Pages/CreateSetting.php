<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Traits\TranslatableFormMutator;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
    // use TranslatableFormMutator;
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['value'] = array_map(function ($item) {
    //         $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
    //         $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
    //         return [
    //             'key' => $keyTranslations,
    //             'value' => $valueTranslations,
    //         ];
    //     }, $data['value']);

    //     $data['value'] = json_encode($data['value'], JSON_UNESCAPED_UNICODE);
    //     return $data;
    // }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // تحويل بيانات info
        $info = array_map(function ($item) {
            $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
            $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
            return [
                'key' => $keyTranslations,
                'value' => $valueTranslations,
            ];
        }, $data['value']['info'] ?? []);

        // تحويل بيانات contacts (إن وجدت)
        $contacts = null;
        if (isset($data['value']['contacts']) && $data['key'] !== 'terms') {
            $contacts = array_map(function ($item) {
                $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
                $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
                return [
                    'key' => $keyTranslations,
                    'value' => $valueTranslations,
                ];
            }, $data['value']['contacts']);
        }

        // تحويل بيانات links (إن وجدت)
        $links = null;
        if (isset($data['value']['links']) && $data['key'] !== 'terms') {
            $links = array_map(function ($item) {
                $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
                $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
                return [
                    'key' => $keyTranslations,
                    'value' => $valueTranslations,
                ];
            }, $data['value']['links']);
        }

        // تجميع البيانات
        $data['value'] = [
            'info' => $info,
            'contacts' => $contacts,
            'links' => $links,
        ];

        return $data;
    }
}
