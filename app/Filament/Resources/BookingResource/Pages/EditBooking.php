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
            $userLocale = $user->locale ?? config('app.locale');
            $workspaceName = 'غير محددة';
            if ($record->workspace && is_array($record->workspace->name)) {
                if (isset($record->workspace->name[$userLocale])) {
                    $workspaceName = $record->workspace->name[$userLocale];
                } else {
                    $workspaceName = $record->workspace->name[config('app.locale')] ?? array_values($record->workspace->name)[0] ?? 'غير محددة';
                }
            } else if ($record->workspace && is_string($record->workspace->name)) {
                $workspaceName = $record->workspace->name;
            }

            if ($user && $user->device_token) {
                $notificationTitle = '';
                $notificationBody = '';
                $customData = [
                    'booking_id' => (string) $record->id,
                    'status' => (string) $record->status,
                    'workspace_name' => (string) $workspaceName,
                ];

                switch ($record->status) {
                    case 'confirmed':
                        $notificationTitle = 'تم تأكيد حجزك!';
                        $notificationBody = 'تم تأكيد حجزك لمساحة العمل **' . $workspaceName . '** بنجاح، بإمكانك التحقق من اسم المستخدم وكلمة المرور.';
                        break;
                    case 'cancelled':
                        $notificationTitle = 'تم إلغاء حجزك!';
                        $notificationBody = 'للأسف، تم إلغاء حجزك لمساحة العمل **' . $workspaceName . '** .';
                        break;
                    case 'pending':
                        $notificationTitle = 'حالة حجزك معلقة';
                        $notificationBody = 'حجزك لمساحة العمل **' . $workspaceName . '** لا يزال بانتظار التأكيد.';
                        break;
                    default:
                        $notificationTitle = 'تحديث حالة الحجز';
                        $notificationBody = 'تم تحديث حالة حجزك لمساحة العمل **' . $workspaceName . '**.';
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
