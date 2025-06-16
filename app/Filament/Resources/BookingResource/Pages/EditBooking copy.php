<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification; // Import the Notification class
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // You can access the updated record here
        $booking = $this->getRecord();
        // dd($booking);
        $user = $booking->user; // Assuming booking has a user relationship

        Notification::make()
            ->title('تم حفظ التغييرات بنجاح!')
            ->body('سيتم حالاً تحديث حجز رقم المقعد: ' . $booking->seat_number)
            ->success()
            ->send();

        // If you want to redirect after save, you can do it here.
        // For example, to redirect back to the list page:
        // $this->redirect(BookingResource::getUrl('index'));

        // Check if there's a user associated and they have a device token
        if ($user) { // Assuming 'fcm_token' column on your User model && $user->device_token
            $credentialsFilePath = storage_path('app/firebase/firebase-credentials.json'); // Make sure this path is correct and accessible
            try {
                $client = new GoogleClient();
                $client->setAuthConfig($credentialsFilePath);
                // dd($client);

                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->refreshTokenWithAssertion();
                $token = $client->getAccessToken();

                $access_token = $token['access_token'];

                $headers = [
                    "Authorization: Bearer $access_token",
                    'Content-Type: application/json'
                ];

                // Dynamically set notification title and body based on booking status
                $notificationTitle = '';
                $notificationBody = '';

                switch ($booking->status) {
                    case 'confirmed':
                        $notificationTitle = 'تم تأكيد حجزك!';
                        $notificationBody = 'تم تأكيد حجزك رقم المقعد ' . $booking->seat_number . ' بنجاح.';
                        break;
                    case 'cancelled':
                        $notificationTitle = 'تم إلغاء حجزك!';
                        $notificationBody = 'للأسف، تم إلغاء حجزك رقم المقعد ' . $booking->seat_number . '.';
                        break;
                    case 'pending':
                        $notificationTitle = 'حالة حجزك معلقة';
                        $notificationBody = 'حجزك رقم المقعد ' . $booking->seat_number . ' لا يزال بانتظار التأكيد.';
                        break;
                    default:
                        $notificationTitle = 'تحديث حالة الحجز';
                        $notificationBody = 'تم تحديث حالة حجزك رقم المقعد ' . $booking->seat_number . '.';
                        break;
                }

                $data = [
                    "message" => [
                        // Instead of a topic, send to a specific device token
                        // "token" => $user->device_token, // Use the user's specific FCM token
                        "token" => auth()->user()->device_token, // Use the user's specific FCM token
                        // "topic" => "tawfeeq",
                        "notification" => [
                            "title" => $notificationTitle,
                            "body" => $notificationBody,
                        ],
                        "apns" => [
                            "payload" => [
                                "aps" => [
                                    "sound" => "default"
                                ]
                            ]
                        ],
                        // You can add custom data payload here if needed for your app
                        // "data" => [
                        //     "booking_id" => $booking->id,
                        //     "status" => $booking->status
                        // ]
                    ]
                ];
                $payload = json_encode($data);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/i-hup-420a6/messages:send');
                // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send');

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($response === false) {
                    Log::error("FCM Curl Error: " . $err);
                    Notification::make()
                        ->title('خطأ في إرسال الإشعار')
                        ->body('فشل إرسال إشعار الدفع للمستخدم.')
                        ->danger()
                        ->send();
                } else {
                    $responseData = json_decode($response, true);
                    if (isset($responseData['error'])) {
                        Log::error("FCM Error Response: " . json_encode($responseData['error']));
                        Notification::make()
                            ->title('خطأ في إرسال الإشعار')
                            ->body('فشل إرسال إشعار الدفع للمستخدم: ' . ($responseData['error']['message'] ?? 'خطأ غير معروف'))
                            ->danger()
                            ->send();
                    } else {
                        // Optionally, log success or show a success notification for the secretary
                        Notification::make()
                            ->title('تم التحديث بنجاح!')
                            ->body('تم تحديث حجز رقم المقعد: ' . $booking->seat_number)
                            ->success()
                            ->send();
                        Log::info("FCM Notification sent successfully for booking ID: " . $booking->id);
                        Log::info("FCM Response: " . $response);
                    }
                }
            } catch (\Exception $e) {
                Log::error("FCM Exception: " . $e->getMessage());
                Notification::make()
                    ->title('خطأ غير متوقع')
                    ->body('حدث خطأ أثناء محاولة إرسال إشعار الدفع.')
                    ->danger()
                    ->send();
            }
        } else {
            // Optional: Notify secretary if user or FCM token is missing
            // Notification::make()
            //     ->title('لا يمكن إرسال إشعار')
            //     ->body('المستخدم غير موجود أو لا يوجد رمز FCM للجهاز.')
            //     ->warning()
            //     ->send();
        }
    }
}
