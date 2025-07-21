<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|min:10|unique:users',
            'specialty' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $phone_verification_code = rand(1000, 9999);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'specialty' => $validated['specialty'],
            'password' => bcrypt($validated['password']),
            'phone_verification_code' => $phone_verification_code,
        ]);
        $token = $user->createToken('api_token')->plainTextToken;

        return $this->apiResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], __('messages.success'), 200);
    }

    // public function verifyPhone(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|exists:users,phone',
    //         'code' => 'required|numeric',
    //     ]);

    //     $user = User::where('phone', $request->phone)->first();

    //     if ($user->phone_verification_code == $request->code) {
    //         $user->phone_verified_at = now();
    //         $user->phone_verification_code = null; // نمسح الكود بعد التحقق
    //         $user->save();

    //         $userData = [
    //             'id' => $user['id'],
    //             'name' => $user['name'],
    //             'phone' => $user['phone'],
    //             'specialty' => $user['specialty'],
    //             'profile_image' => null,
    //         ];

    //         $data = [
    //             'user' => $userData,
    //             'token' => $user->createToken('api_token')->plainTextToken
    //         ];

    //         return $this->apiResponse($data, __('messages.success'), 200);
    //     }
    //     return $this->apiResponse(null, __('messages.not_found'), 422);
    // }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }
        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;
        // $user = [
        //     'id' => $user['id'],
        //     'name' => $user['name'],
        //     'phone' => $user['phone'],
        //     'specialty' => $user['specialty'],
        //     'profile_image' => null,
        // ];
        // $data = [
        //     'token' => $token,
        //     'user' => $user,
        // ];
        return $this->apiResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], __('messages.success'), 200);

        // return $this->apiResponse($data, __('messages.success'), 200);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse(null, __('messages.success'), 200);
    }
}
