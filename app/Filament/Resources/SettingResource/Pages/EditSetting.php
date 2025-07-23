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
        // تحويل البيانات إلى صيغة مناسبة
        $data['value'] = array_map(function ($item) {
            $keyTranslations = collect($item['key_translations'])->pluck('value', 'locale')->all();
            $valueTranslations = collect($item['value_translations'])->pluck('value', 'locale')->all();
            return [
                'key' => $keyTranslations,
                'value' => $valueTranslations,
            ];
        }, $data['value']);

        // التأكد من أن البيانات تُخزن كـ JSON صالح
        $data['value'] = json_encode($data['value'], JSON_UNESCAPED_UNICODE);

        return $data;
    }
}
