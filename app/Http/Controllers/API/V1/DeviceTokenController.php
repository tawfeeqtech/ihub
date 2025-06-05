<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class DeviceTokenController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->device_token = $request->token;
        $user->save();
        return $this->apiResponse(null, __('messages.device_token'), 200);
    }
    public function testSendNot()
    {
        $firebase_credentials = storage_path('app/firebase/firebase-credentials.json');
        $factory = (new Factory)->withServiceAccount($firebase_credentials);
        $messaging = $factory->createMessaging();
        dd($messaging);
        $message = CloudMessage::withTarget('token', 'DEVICE_TOKEN')
            ->withNotification(Notification::create('عنوان', 'محتوى الرسالة'));

        $messaging->send($message);
    }
}
