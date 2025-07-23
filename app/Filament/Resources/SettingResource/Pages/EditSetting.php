<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Traits\TranslatableFormMutator;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;
    use TranslatableFormMutator;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // تحويل البيانات إلى صيغة مناسبة للحفظ في قاعدة البيانات
        $data['value'] = array_map(function ($item) {
            $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
            $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
            return [
                'key' => $keyTranslations,
                'value' => $valueTranslations,
            ];
        }, $data['value']);

        $data['value'] = json_encode($data['value'], JSON_UNESCAPED_UNICODE);

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // تحويل البيانات من قاعدة البيانات إلى صيغة النموذج
        $value = $this->record->value; // البيانات من قاعدة البيانات (مصفوفة بسبب $casts)

        // تحويل البيانات إلى صيغة Repeater
        $data['value'] = collect($value)->map(function ($item) {
            $keyTranslations = collect($item['key'])->map(function ($value, $locale) {
                return ['locale' => $locale, 'value' => $value];
            })->values()->all();

            $valueTranslations = collect($item['value'])->map(function ($value, $locale) {
                return ['locale' => $locale, 'value' => $value];
            })->values()->all();

            return [
                'key_translations' => $keyTranslations,
                'value_translations' => $valueTranslations,
            ];
        })->all();

        return $data;
    }
}
