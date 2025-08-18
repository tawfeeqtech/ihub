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

            // Explicitly load the user and workspace to avoid static analysis errors
            $user = \App\Models\User::find($record->user_id);
            if (!$user) {
                throw new Exception('User not found for booking');
            }

            // Also explicitly load the workspace
            $workspace = \App\Models\Workspace::find($record->workspace_id);
            if ($workspace) {
                $record->setRelation('workspace', $workspace);
            }
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
                    'status' => (string) $record->status->value,
                    'workspace_name' => (string) $workspaceName,
                ];

                switch ($record->status->value) {
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

                error_log("Attempting to send notification to device token: " . $user->device_token);

                $fcmResult = $this->sendFirebasePushNotification(
                    $user->device_token,
                    $notificationTitle,
                    $notificationBody,
                    $customData
                );

                if ($fcmResult !== true) {
                    $fcmResultError = $fcmResult;
                    error_log("FCM Error for user {$user->id}: " . json_encode($fcmResultError));

                    // Handle various FCM error conditions that indicate an invalid device token
                    $shouldClearToken = false;
                    $errorMessage = '';

                    if (is_array($fcmResultError) && isset($fcmResultError['error'])) {
                        $error = $fcmResultError['error'];
                        $status = $error['status'] ?? '';
                        $message = $error['message'] ?? '';
                        $errorCode = isset($error['details'][0]['errorCode']) ? $error['details'][0]['errorCode'] : '';

                        // Check for conditions that indicate the device token is no longer valid
                        if ($status === 'NOT_FOUND' ||
                            $status === 'UNREGISTERED' ||
                            $errorCode === 'UNREGISTERED' ||
                            strpos($message, 'Requested entity was not found') !== false) {
                            $shouldClearToken = true;
                            $errorMessage = "FCM token invalid: status={$status}, errorCode={$errorCode}";
                        }
                    }

                    if ($shouldClearToken) {
                        // Commit the transaction first to save any other changes
                        DB::commit();

                        // Clear the device token outside of the transaction
                        $user->device_token = null;
                        $user->save();
                        error_log("Cleared invalid device token for user {$user->id}. {$errorMessage}");

                        // Start a new transaction for the rest of the operation
                        DB::beginTransaction();
                    }

                    throw new Exception(__('notifications.fcmResultErrorException') . json_encode($fcmResultError));
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
                ->body(is_array($fcmResultError) ? json_encode($fcmResultError) : $fcmResultError)
                ->danger()
                ->send();

            throw $e;
        }
    }
}
