<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponseTrait;

class SettingController extends Controller
{
    use ApiResponseTrait;

    public function show($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return $this->apiResponse(null, "المحتوى غير موجود", 404);
        }

        $data = [
            'key' => $setting->key,
            'content' => $setting->value
        ];

        return $this->apiResponse($data, "success", 200);
    }
}
