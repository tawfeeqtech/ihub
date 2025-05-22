<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function show(Request $request)
    {
        $user = $request->user();
        return $this->apiResponse(new UserProfileResource($user), "Success", 200);
    }

    // تحديث البيانات الشخصية
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'specialty' => 'nullable|string|max:255',
            // 'password' => 'nullable|string|min:8|confirmed',
        ], [
            'phone.unique' => 'حاول رقم هاتف آخر',
        ]);

        // if (isset($validated['password'])) {
        //     $validated['password'] = Hash::make($validated['password']);
        // }

        $user->update($validated);
        return $this->apiResponse(new UserProfileResource($user), "Profile updated successfully", 201);
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return $this->apiResponse(null, "فشل في تحميل الصورة", 422);
        }

        $file = $request->file('image');


        if (!$file || !$file->isValid()) {
            return $this->apiResponse(null, "فشل في رفع الصورة", 422);
        }


        $user = $request->user();

        $path = 'uploads/profile_images/' . auth()->id();
        $path_image = $this->uploadImage($path, $request, 'image');

        $user->profile_image = $path_image;
        $user->save();
        $data = [
            'profile_image' => asset($user->profile_image),
        ];
        return $this->apiResponse($data, "تم تحديث صورة الملف الشخصي", 200);
    }
}
