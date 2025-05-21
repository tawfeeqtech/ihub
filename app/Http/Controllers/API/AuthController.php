<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
            'phone' => 'required|string|max:20|unique:users',
            'specialty' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $phone_verification_code = rand(1000, 9999);

        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'specialty' => $validated['specialty'],
            'password' => bcrypt($validated['password']),
            'phone_verification_code' => $phone_verification_code,
        ]);

        $data = [
            'code' => $phone_verification_code,
        ];

        return $this->apiResponse($data, "Phone Verification Code", 200);
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'code' => 'required|numeric',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user->phone_verification_code == $request->code) {
            $user->phone_verified_at = now();
            $user->phone_verification_code = null; // نمسح الكود بعد التحقق
            $user->save();

            $data = [
                'user' => $user,
                'token' => $user->createToken('api_token')->plainTextToken
            ];

            return $this->apiResponse($data, "Phone verified successfully", 200);
        }
        return $this->apiResponse(null, "Invalid verification code", 422);
    }


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

        $token = $user->createToken('api_token')->plainTextToken;
        $user = [
            'id' => $user['id'],
            'name' => $user['name'],
            'phone' => $user['phone'],
            'specialty' => $user['specialty'],
        ];
        $data = [
            'token' => $token,
            'user' => $user,
        ];


        return $this->apiResponse($data, "Login successfully", 200);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse(null, "Logged out successfully", 200);
    }
}
