<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

use Kreait\Firebase\Messaging\InvalidMessage;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(
            storage_path('app/firebase/firebase-credentials.json')
        );

        $this->messaging = $factory->createMessaging();
    }

    public function sendToDevice(string $deviceToken, string $title, string $body): void
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        $this->messaging->send($message);
    }

    //     Inject and call it from your Notification class or controller like this:
    // $firebaseService = new \App\Services\FirebaseNotificationService();

    // $firebaseService->sendToDevice($user->device_token, 'New Message', 'You have a new message from the app.');

}
