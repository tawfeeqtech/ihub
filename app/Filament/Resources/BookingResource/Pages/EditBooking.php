<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Traits\PushNotificationTrait;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class EditBooking extends EditRecord
{
    use PushNotificationTrait;
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {

        DB::beginTransaction();
        $fcmResultError = '';
        try {
            $record->fill($data)->save();

            $user = $record->user;
            if ($user && $user->device_token) {
                $notificationTitle = '';
                $notificationBody = '';
                $customData = [
                    'booking_id' => (string) $record->id,
                    'status' => (string) $record->status,
                ];

                switch ($record->status) {
                    case 'confirmed':
                        $notificationTitle = 'تم تأكيد حجزك!';
                        $notificationBody = 'تم تأكيد حجزك رقم المقعد ' . $record->seat_number . ' بنجاح.';
                        break;
                    case 'cancelled':
                        $notificationTitle = 'تم إلغاء حجزك!';
                        $notificationBody = 'للأسف، تم إلغاء حجزك رقم المقعد ' . $record->seat_number . '.';
                        break;
                    case 'pending':
                        $notificationTitle = 'حالة حجزك معلقة';
                        $notificationBody = 'حجزك رقم المقعد ' . $record->seat_number . ' لا يزال بانتظار التأكيد.';
                        break;
                    default:
                        $notificationTitle = 'تحديث حالة الحجز';
                        $notificationBody = 'تم تحديث حالة حجزك رقم المقعد ' . $record->seat_number . '.';
                        break;
                }

                $fcmResult = $this->sendFirebasePushNotification(
                    $user->device_token,
                    $notificationTitle,
                    $notificationBody,
                    $customData
                );

                if ($fcmResult !== true) {
                    $fcmResultError = $fcmResult;
                    throw new Exception('فشل إرسال إشعار الدفع للمستخدم. السبب: ' . $fcmResult);
                }
            } else {
                throw new Exception('لا يمكن إرسال إشعار الدفع: المستخدم غير موجود أو لا يوجد رمز جهاز FCM.');
            }

            DB::commit();

            Notification::make()
                ->title('تم حفظ التغييرات بنجاح!')
                ->body('تم تحديث حجز رقم المقعد: ' . $record->seat_number)
                ->success()
                ->send();

            // Filament expects the updated model instance to be returned
            return $record;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Booking update or FCM failed and transaction rolled back: " . $e->getMessage(), ['booking_id' => $record->id ?? 'N/A']);

            Notification::make()
                ->title('خطأ في حفظ الحجز')
                ->body($fcmResultError)
                ->danger()
                ->send();

            throw $e;
        }
    }
}
