<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmController extends Controller
{
    use ApiResponseTrait;
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = User::find(auth()->id());
        $user->update(['device_token' => $request->fcm_token]);
        $data = ['device_token' => $request->fcm_token];
        return $this->apiResponse($data, __('messages.device_token'), 200);
    }
    // public function testSendNot()
    // {
    //     $firebase_credentials = storage_path('app/firebase/firebase-credentials.json');
    //     $factory = (new Factory)->withServiceAccount($firebase_credentials);
    //     $messaging = $factory->createMessaging();
    //     dd($messaging);
    //     $message = CloudMessage::withTarget('token', 'DEVICE_TOKEN')
    //         ->withNotification(Notification::create('عنوان', 'محتوى الرسالة'));

    //     $messaging->send($message);
    // }
}
