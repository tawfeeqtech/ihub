<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Traits\TranslatableFormMutator;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;
    // use TranslatableFormMutator;
    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $value = $this->record->value;
        $data['value']['info'] = array_map(function ($item) {
            $keyTranslations = [];
            foreach ($item['key'] as $locale => $value) {
                $keyTranslations[] = ['locale' => $locale, 'value' => $value];
            }
            $valueTranslations = [];
            foreach ($item['value'] as $locale => $value) {
                $valueTranslations[] = ['locale' => $locale, 'value' => $value];
            }
            return [
                'key_translations' => $keyTranslations,
                'value_translations' => $valueTranslations,
            ];
        }, $value['info'] ?? []);
        $data['value']['contacts'] = null;
        if (isset($value['contacts']) && $this->record->key !== 'terms') {
            $data['value']['contacts'] = array_map(function ($item) {
                $keyTranslations = [];
                foreach ($item['key'] as $locale => $value) {
                    $keyTranslations[] = ['locale' => $locale, 'value' => $value];
                }
                $valueTranslations = [];
                foreach ($item['value'] as $locale => $value) {
                    $valueTranslations[] = ['locale' => $locale, 'value' => $value];
                }
                return [
                    'key_translations' => $keyTranslations,
                    'value_translations' => $valueTranslations,
                ];
            }, $value['contacts']);
        }

        $data['value']['links'] = null;
        if (isset($value['links']) && $this->record->key !== 'terms') {
            $data['value']['links'] = array_map(function ($item) {
                $keyTranslations = [];
                foreach ($item['key'] as $locale => $value) {
                    $keyTranslations[] = ['locale' => $locale, 'value' => $value];
                }
                $valueTranslations = [];
                foreach ($item['value'] as $locale => $value) {
                    $valueTranslations[] = ['locale' => $locale, 'value' => $value];
                }
                return [
                    'key_translations' => $keyTranslations,
                    'value_translations' => $valueTranslations,
                ];
            }, $value['links']);
        }

        return $data;
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // تحويل البيانات إلى صيغة مناسبة للحفظ في قاعدة البيانات
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

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     // تحويل البيانات من قاعدة البيانات إلى صيغة النموذج
    //     $value = $this->record->value; // البيانات من قاعدة البيانات (مصفوفة بسبب $casts)

    //     // تحويل البيانات إلى صيغة Repeater
    //     $data['value'] = collect($value)->map(function ($item) {
    //         $keyTranslations = collect($item['key'])->map(function ($value, $locale) {
    //             return ['locale' => $locale, 'value' => $value];
    //         })->values()->all();

    //         $valueTranslations = collect($item['value'])->map(function ($value, $locale) {
    //             return ['locale' => $locale, 'value' => $value];
    //         })->values()->all();

    //         return [
    //             'key_translations' => $keyTranslations,
    //             'value_translations' => $valueTranslations,
    //         ];
    //     })->all();

    //     return $data;
    // }
}
