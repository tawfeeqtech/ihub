<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\App;

class SettingController extends Controller
{
    use ApiResponseTrait;

    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return $this->apiResponse(null, __('messages.not_found'), 404);
        }

        $lang = App::getLocale();
        $value = $setting->value;
        if (is_string($value)) {
            $value = json_decode(stripslashes($value), true) ?? [];
        }

        // $data = collect($value)->map(function ($item) use ($lang) {
        //     return [
        //         'key' => data_get($item, "key.{$lang}", data_get($item, 'key.en', 'غير متوفر')),
        //         'description' => data_get($item, "value.{$lang}", data_get($item, 'value.en', 'غير متوفر')),
        //     ];
        // })->all();
        $info = collect($value['info'] ?? [])->map(function ($item) use ($lang) {
            return [
                'key' => data_get($item, "key.{$lang}", data_get($item, 'key.en', 'غير متوفر')),
                'description' => data_get($item, "value.{$lang}", data_get($item, 'value.en', 'غير متوفر')),
            ];
        })->all();

        // تحويل بيانات contacts
        $contacts = null;
        if (isset($value['contacts']) && $setting->key !== 'terms') {
            $contacts = collect($value['contacts'])->map(function ($item) use ($lang) {
                return [
                    'key' => data_get($item, "key.{$lang}", data_get($item, 'key.en', 'غير متوفر')),
                    'description' => data_get($item, "value.{$lang}", data_get($item, 'value.en', 'غير متوفر')),
                ];
            })->all();
        }

        // تحويل بيانات links
        $links = null;
        if (isset($value['links']) && $setting->key !== 'terms') {
            $links = collect($value['links'])->map(function ($item) use ($lang) {
                return [
                    'key' => data_get($item, "key.{$lang}", data_get($item, 'key.en', 'غير متوفر')),
                    'description' => data_get($item, "value.{$lang}", data_get($item, 'value.en', 'غير متوفر')),
                ];
            })->all();
        }

        $data = [
            'info' => $info,
            'contacts' => $contacts,
            'links' => $links,
        ];


        return $this->apiResponse($data, __('messages.success'), 200);
    }
}
