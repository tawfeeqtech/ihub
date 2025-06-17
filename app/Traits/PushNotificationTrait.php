<?php

namespace App\Traits;

use Google\Client;
use Illuminate\Support\Facades\Log;

trait PushNotificationTrait
{
    /**
     * Sends a Firebase Cloud Message (FCM) to a specific user's device.
     *
     * @param string $fcmToken The FCM device token of the recipient.
     * @param string $title The title of the notification.
     * @param string $body The body/content of the notification.
     * @param array $data Optional custom data payload.
     * @return bool True if the notification was sent successfully, false otherwise.
     */
    protected function sendFirebasePushNotification(string $fcmToken, string $title, string $body, array $data = []): true|string
    {
        $credentialsFilePath = storage_path('app/firebase/firebase-credentials.json');

        $projectId = config('services.fcm.project_id');
        if (!$projectId) {
            Log::error("Firebase project ID not configured in config/services.php or .env.");
            return "خطأ في الإعداد: معرف مشروع Firebase غير مهيأ.";
        }

        try {
            $client = new Client();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();
            $access_token = $token['access_token'];

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];

            $message = [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "default"
                        ]
                    ]
                ],
            ];

            // Add custom data if provided
            if (!empty($data)) {
                $message['data'] = $data;
            }

            $payload = json_encode(["message" => $message]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Consider setting this to true for production with proper CA certs
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                $errorMessage = "خطأ cURL: " . $err;
                Log::error("FCM Curl Error: " . $errorMessage);
                return $errorMessage;
            } else {
                $responseData = json_decode($response, true);
                if (isset($responseData['error'])) {
                    $errorMessage = "خطأ استجابة FCM: " . ($responseData['error']['message'] ?? 'خطأ غير معروف في استجابة FCM');
                    Log::error("FCM Error Response: " . json_encode($responseData['error']));
                    return $errorMessage;
                } else {
                    Log::info("FCM Notification sent successfully to token: " . $fcmToken);
                    return true;
                }
            }
        } catch (\Exception $e) {
            $errorMessage = "استثناء FCM: " . $e->getMessage();
            Log::error("FCM Exception: " . $errorMessage);
            return $errorMessage;
        }
    }
}
