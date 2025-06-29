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
            Log::info("userlocale:: " . $user->locale);
            $userLocale = $user->locale ?? config('app.locale');
            app()->setLocale($user?->current_locale);
            $workspaceName = __('notifications.EditBooking.defaultWorkspaceName');
            if ($record->workspace && is_array($record->workspace->name)) {
                if (isset($record->workspace->name[$userLocale])) {
                    $workspaceName = $record->workspace->name[$userLocale];
                } else {
                    $workspaceName = $record->workspace->name[config('app.locale')] ?? array_values($record->workspace->name)[0] ?? __('notifications.EditBooking.defaultWorkspaceName');
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
                        $notificationTitle =  __('notifications.notificationTitle.confirmed');
                        $notificationBody = __('notifications.notificationBody.confirmed', [
                            'workspaceName' => $workspaceName
                        ]);
                        break;
                    case 'cancelled':
                        $notificationTitle = __('notifications.notificationTitle.cancelled');
                        $notificationBody = __('notifications.notificationBody.cancelled', [
                            'workspaceName' => $workspaceName
                        ]);
                        break;
                    case 'pending':
                        $notificationTitle = __('notifications.notificationTitle.pending');
                        $notificationBody = __('notifications.notificationBody.pending', [
                            'workspaceName' => $workspaceName
                        ]);
                        break;
                    default:
                        $notificationTitle = __('notifications.notificationTitle.default');
                        $notificationBody = __('notifications.notificationBody.default', [
                            'workspaceName' => $workspaceName
                        ]);
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
                    throw new Exception(__('notifications.fcmResultErrorException') . $fcmResult);
                }
            } else {
                throw new Exception(__('notifications.ErrorException'));
            }

            DB::commit();
            app()->setLocale(auth()->user()?->current_locale);

            Notification::make()
                ->title(__('notifications.EditBooking.notificationTitle'))
                ->body(__('notifications.EditBooking.notificationBody') . $record->seat_number)
                ->success()
                ->send();

            // Filament expects the updated model instance to be returned
            return $record;
        } catch (Exception $e) {
            DB::rollBack();

            // Log::error("Booking update or FCM failed and transaction rolled back: " . $e->getMessage(), ['booking_id' => $record->id ?? 'N/A']);

            Notification::make()
                ->title(__('notifications.EditBooking.catchErrorTitle'))
                ->body($fcmResultError)
                ->danger()
                ->send();

            throw $e;
        }
    }
}
