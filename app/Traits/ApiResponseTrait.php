<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $message = null, $status = null)
    {
        $array = [
            'data' => $data,
            'message' => $message,
            'status' => $status
        ];
        return response($array, $status);
    }

    public function uploadImage($uploadPath, $request, $requestName)
    {
        $file = $request->file($requestName);
        $filename = time() . '.' . $file->extension();

        // أنشئ المجلد إذا مش موجود
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }

        $file->move(public_path($uploadPath), $filename);

        return $uploadPath . "/" . $filename;
    }
}
