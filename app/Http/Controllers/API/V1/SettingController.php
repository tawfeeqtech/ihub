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

        $lang = App::getLocale(); // <-- اللغة الحالية حسب Accept-Language

        $data = [
            'key' => $setting->key,
            'content' => $setting->value[$lang] ?? $setting->value['en'] ?? '', // <-- ترجمة ذكية
        ];

        return $this->apiResponse($data, __('messages.success'), 200);
    }
}
