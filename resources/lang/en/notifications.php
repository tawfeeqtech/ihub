<?php
return [
    'unread_message' => 'You have an unread message(s) from :sender',
    'workspace_reservation_secretary' => "There is a new request to reserve a workspace :workspaceName from user :sender",
    'workspace_reservation_user' => "New details for the 'Workspace Reservation' request for :space",

    'ServiceRequest_secretary' => "There is a service request :serviceName from user  :sender",
    'ServiceRequest_user' => "The service request :serviceName has been confirmed",

    "sendServiceRequestNotification.serviceRequests" => "Service Requests",


    "notificationTitle.confirmed" => "Your booking has been confirmed!",
    "notificationTitle.cancelled" => 'Your booking has been cancelled!',
    "notificationTitle.pending" => 'Your booking status is pending!',
    "notificationTitle.default" => 'Update booking status!',

    "notificationBody.confirmed" => 'Your workspace booking :workspaceName has been successfully confirmed. You can verify your username and password.',
    "notificationBody.cancelled" => 'Unfortunately, your booking for workspace :workspaceName has been cancelled.',
    "notificationBody.pending" => 'Your workspace booking :workspaceName is still awaiting confirmation.',
    "notificationBody.default" => 'Your workspace booking status has been updated :workspaceName .',

    'fcmResultErrorException' => 'Failed to send push notification to user. Reason:',
    'ErrorException' => 'Push notification cannot be sent: User does not exist or FCM device token is missing.',

    'EditBooking.notificationTitle' => 'Changes saved successfully!',
    'EditBooking.notificationBody' => 'Seat reservation number updated:!',

    'EditBooking.catchErrorTitle' => 'Error saving reservation',

    'EditBooking.defaultWorkspaceName' => 'Unspecified',
];
