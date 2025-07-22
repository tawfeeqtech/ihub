<?php

namespace App\Services;

use App\Events\ServiceRequestNotification;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Support\Facades\App;

class NotificationService
{
    protected $messaging;
    protected $credentialsPath;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
        $this->credentialsPath = base_path() . '/storage/app/firebase/firebase-credentials.json';
        // Log::info('NotificationService initialized', [
        //     'firebase_credentials' => $this->credentialsPath,
        // ]);
    }

    // Send unread message notification
    public function sendUnreadMessageNotification(User $recipient, User $sender, bool $isSecretary = false, ?string $conversationId = null): void
    {
        // $message = $isSecretary
        //     ? "You have an unread message(s) from {$sender->name}"
        //     : "You have an unread message(s) from {$sender->name} for the space";


        // Database notification for Filament (secretary)
        if ($isSecretary) {
            // $this->setLocale($recipient);

            $message = $isSecretary
                ? __('notifications.unread_message', ['sender' => $sender->name])
                : __('notifications.unread_message', ['sender' => $sender->name]) . " for the space";


            Notification::make()
                ->title($message)
                ->success()
                ->sendToDatabase($recipient);
            event(new DatabaseNotificationsSent($recipient));
        }

        // Push notification via FCM
        if ($recipient->device_token) {
            $this->sendFcmNotification(
                $recipient->device_token,
                $message,
                'New Message',
                [
                    'screen' => 'chat', // Target screen in the app
                    'conversation_id' => $conversationId, // ID to load the correct conversation
                ]
            );
        }
    }

    // Send workspace reservation notification
    public function sendWorkspaceReservationNotification(User $recipient, User $sender, $workspace, bool $isSecretary = false, ?string $reservationId = null): void
    {
        $senderLocale = $sender->locale ?? 'en';
        $secretaryLocale = $recipient->locale ?? 'en';
        // Log::info("senderLocale " . $senderLocale);
        // Log::info("secretaryLocale " . $secretaryLocale);
        $this->setLocale($recipient);
        $translatedSpaceNameSecretary = $workspace->name[$secretaryLocale] ?? $workspace->name['en'] ?? 'Unknown Workspace';
        $formattedSpaceNameSecretary = '<span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">' . htmlspecialchars($translatedSpaceNameSecretary) . '</span>';


        // $this->setLocale($sender);
        // $translatedSpaceNameUser = $workspace->name[$senderLocale] ?? $workspace->name['en'] ?? 'Unknown Workspace';
        // $formattedSpaceNameUser = '<span class="font-semibold text-success-600 dark:text-success-400 underline">' . htmlspecialchars($translatedSpaceNameUser) . '</span>';

        // $message = $isSecretary
        //     ? "There is a new 'Workspace Reservation' request from {$sender->name}"
        //     : "New details for the 'Workspace Reservation' request for {$spaceName}";

        // $translatedSpaceName = __("filament.ServiceRequest.types.{$spaceName}") === "filament.ServiceRequest.types.{$serviceName}"
        //     ? $spaceName
        //     : '<span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">' . __("filament.ServiceRequest.types.{$serviceName}") . '</span>';


        // $message = $isSecretary
        //     ? __('notifications.workspace_reservation_secretary', ['sender' => $sender->name, 'workspaceName' => $spaceName])
        //     : __('notifications.workspace_reservation_user', ['space' => $spaceName]);

        $messageSecretary =  __('notifications.workspace_reservation_secretary', [
            'sender' => $sender->name,
            'workspaceName' => $formattedSpaceNameSecretary
        ]);

        if ($isSecretary && $reservationId) {
            Notification::make()
                ->title($messageSecretary)
                ->success()
                ->actions([
                    Action::make('view')
                        ->label('View Reservation')
                        ->url(url: fn() => url("admin/bookings/{$reservationId}/edit"))
                        ->color('primary')->markAsRead(),
                ])
                ->sendToDatabase($recipient);
            // event(new DatabaseNotificationsSent($recipient));
        } elseif ($isSecretary) {
            Notification::make()
                ->title($messageSecretary)
                ->success()
                ->sendToDatabase($recipient);
            // event(new DatabaseNotificationsSent($recipient));
        }

        // Push notification via FCM
        // if ($recipient->device_token) {
        $this->setLocale($senderLocale);
        $translatedSpaceNameUser = $workspace->name[$senderLocale] ?? $workspace->name['en'] ?? 'Unknown Workspace';
        $formattedSpaceNameUser = '<span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">' . htmlspecialchars($translatedSpaceNameUser) . '</span>';

        $messageUser =  __('notifications.workspace_reservation_user', [
            'space' => $formattedSpaceNameUser
        ]);

        Notification::make()
            ->title($messageUser)
            ->success()
            ->sendToDatabase($recipient);
        //     $this->sendFcmNotification(
        //         $recipient->device_token,
        //         $message,
        //         'Workspace Reservation',
        //         [
        //             'screen' => 'reservation_details', // Target screen in the app
        //             'reservation_id' => $reservationId, // ID to load the reservation
        //             'space_name' => $spaceName,
        //         ]
        //     );
        // }
    }

    // Send service request notification
    public function sendServiceRequestNotification(User $recipient, User $sender, string $serviceName, bool $isSecretary = false): void //, ?string $serviceRequestId = null
    {
        // $message = $isSecretary
        //     ? "There is a service request '{$serviceName}' from {$sender->name}"
        //     : "The service request '{$serviceName}' has been confirmed";



        // Database notification for Filament (secretary)
        if ($isSecretary) {

            $this->setLocale($recipient);
            // $translatedServiceName = __("filament.ServiceRequest.types.{$serviceName}");

            $translatedServiceName = __("filament.ServiceRequest.types.{$serviceName}") === "filament.ServiceRequest.types.{$serviceName}"
                ? $serviceName
                : '<span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--danger-400);--c-600:var(--danger-600);">' . __("filament.ServiceRequest.types.{$serviceName}") . '</span>';
            $message = $isSecretary
                ? __('notifications.ServiceRequest_secretary', [
                    'serviceName' => $translatedServiceName,
                    'sender' => $sender->name
                ])
                : __('notifications.ServiceRequest_user', ['serviceName' => $translatedServiceName]);

            Notification::make()
                ->title($message)
                ->success()
                ->icon('heroicon-o-bell') // Ensure icon
                ->iconColor('success')
                ->actions([
                    Action::make('view')
                        ->label(__("notifications.sendServiceRequestNotification.serviceRequests"))
                        ->url(url: fn() => url("admin/service-requests"))
                        ->color('primary')->markAsRead(),
                ])
                ->sendToDatabase($recipient);
            event(new DatabaseNotificationsSent($recipient));
            // event(new ServiceRequestNotification($recipient, $message, $isSecretary));
        }

        // Push notification via FCM
        // if ($recipient->device_token) {
        //     $this->sendFcmNotification(
        //         $recipient->device_token,
        //         $message,
        //         'Service Request',
        //         [
        //             'screen' => 'service_request', // Target screen in the app
        //             // 'service_request_id' => $serviceRequestId, // ID to load the service request
        //             'service_name' => $serviceName,
        //         ]
        //     );
        // }
    }


    // protected function sendFcmNotification(string $fcmToken, string $body, string $title): void
    // {
    //     try {
    //         $message = CloudMessage::withTarget('token', $fcmToken)
    //             ->withNotification(FcmNotification::create($title, $body));
    //         Log::info('Attempting to send FCM notification', [
    //             'token' => substr($fcmToken, 0, 10) . '...', // Log partial token for security
    //             'title' => $title,
    //             'body' => $body,
    //         ]);
    //         $this->messaging->send($message);
    //         Log::info('FCM notification sent successfully');
    //     } catch (\Kreait\Firebase\Exception\InvalidArgumentException $e) {
    //         Log::error('FCM configuration error', [
    //             'message' => $e->getMessage(),
    //             'file' => config('services.firebase.credentials'),
    //         ]);
    //         throw $e; // Re-throw to catch in sendMessage
    //     } catch (\Exception $e) {
    //         Log::error('FCM notification failed', [
    //             'message' => $e->getMessage(),
    //             'file' => config('services.firebase.credentials'),
    //         ]);
    //         throw $e;
    //     }
    // }

    protected function sendFcmNotification(string $fcmToken, string $body, string $title, array $data = []): void
    {

        try {
            // Log::info('Attempting to send FCM notification', [
            //     'token' => substr($fcmToken, 0, 10) . '...',
            //     'title' => $title,
            //     'body' => $body,
            //     'data' => $data,
            //     'credentials_path' =>  $this->credentialsPath,
            //     'is_readable' => is_readable($this->credentialsPath),
            // ]);

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(FcmNotification::create($title, $body))
                ->withData($data); // Add custom data for navigation

            $this->messaging->send($message);

            // Log::info('FCM notification sent successfully', [
            //     'token' => substr($fcmToken, 0, 10) . '...',
            // ]);
        } catch (\Kreait\Firebase\Exception\InvalidArgumentException $e) {
            // Log::error('FCM configuration error', [
            //     'message' => $e->getMessage(),
            //     'credentials_path' => $this->credentialsPath,
            //     'is_readable' => is_readable($this->credentialsPath),
            //     'file_exists' => file_exists($this->credentialsPath),
            // ]);
            throw $e;
        } catch (\Exception $e) {
            // Log::error('FCM notification failed', [
            //     'message' => $e->getMessage(),
            //     'credentials_path' => $this->credentialsPath,
            // ]);
            throw $e;
        }
    }

    protected function setLocale($user): void
    {
        // Log::info('current_locale: ' . $user?->current_locale);
        // Log::info('username: ' . $user?->name);
        App::setLocale($user?->current_locale);
    }
}
