<?php

return [
    'booking.sidebar.label' => 'Bookings',
    'booking.table.workspace' => 'workspace',
    'booking.table.package' => 'package',

    'table' => [
        'username' => 'username',
        'id'    => 'id',
        'status'    => 'status',
    ],

    'Conversation' => [
        'label' => 'Conversations',
        'table' => [
            'show_conversation' => 'View conversation',
        ],
    ],
    'ServiceRequest' => [
        'label' => 'Service Requests',
        'table' => [
            'request_type' => 'Request type',
        ],
    ],

    'ServiceRequest.table.status.in_progress' => 'in progress',
    'ServiceRequest.table.status.complete' => 'complete',
    'ServiceRequest.table.status.reject' => 'rejected',

    'ServiceRequest.types.seat_change' => 'Change seat',
    'ServiceRequest.types.cafe_request' => 'Cafe order',

    'ServiceRequest.statuses.pending' => 'Pending',
    'ServiceRequest.statuses.in_progress' => 'In progress',
    'ServiceRequest.statuses.completed' => 'complete',
    'ServiceRequest.statuses.rejected' => 'rejected',

    'Service.label' => 'Services',
    'Service.table.category' => 'category',
    'Service.table.name' => 'Service name',



    'Service.form.Categories.hot' => 'hot drinks',
    'Service.form.Categories.cold' => 'cold drinks',
    'Service.form.Categories.sweets' => 'sweets',

    'Service.form.addActionLabel' => 'Add another language',
    'Service.form.locale' => 'language',

    'Service.form.Section' => 'General information',
    'Service.form.category_translations.label' => 'Multilingual Category',
    'Service.form.category_translations.predefined.label' => 'Choose category',
    'Service.form.category_translations.predefined.placeholder' => 'Choose from the added categories',
    'Service.form.category_translations.custom' => 'Or add a new category',

    'Service.form.name_translations.lable' => 'Multilingual Name',
    'Service.form.name_translations.value' => 'Name',





    'BookingResource.form.seat_number' => 'seat number',
    'BookingResource.form.wifi_username' => 'Wi-Fi username',
    'BookingResource.form.wifi_password' => 'Wi-Fi password',

    'BookingResource.form.status.pending' => 'Awaiting confirmation',
    'BookingResource.form.status.confirmed' => 'Confirmed',
    'BookingResource.form.status.cancelled' => 'Canceled',


    'PackageResource.label' => 'Packages',

    'PackageResource.form.name.day' => 'day',
    'PackageResource.form.name.hour' => 'hour',
    'PackageResource.form.name.week' => 'week',
    'PackageResource.form.name.month' => 'month',

    'PackageResource.table.name' => 'package',
    'PackageResource.table.price' => 'price',
    'PackageResource.table.duration' => 'duration',

    'ListPackages.alert' => 'alert',
    'ListPackages.body' => 'No more packages can be added. You can only edit existing values.',

    'Widgets.Bookings.datasets.label' => 'Bookings',

    'Widgets.AdminBookingsByStatusChart.heading' => 'Bookings by Status',
    'Widgets' => [
        'AdminBookingsByStatusChart' => [
            'labels' => [
                'pending' => 'pending',
                'confirmed' => 'confirmed',
                'cancelled' => 'cancelled',
            ],
        ],
        'AdminServiceRequestsByStatusChart' => [
            'labels' => [
                'pending' => 'pending',
                'in_progress' => 'in progress',
                'completed'  => 'completed',
                'rejected' => 'rejected'
            ],
        ],
    ],

    'Widgets.AdminBookingsChart.heading' => 'Bookings Over Time',
    'Widgets.AdminConversationsActivityChart.heading' => 'Conversations Activity Over Time',
    'Widgets.AdminConversationsActivityChart.datasets.label' => 'New Conversations',

    'Widgets.AdminPackagesByWorkspaceChart.heading' => 'Packages Distribution by Workspace',
    'Widgets.AdminPackagesByWorkspaceChart.datasets.label' => 'Packages',

    'Widgets.AdminServiceRequestsByStatusChart.heading' => 'Service Requests by Status',
    'Widgets.AdminServiceRequestsByStatusChart.datasets.label' => 'Service Requests',

    'Widgets.AdminServicesByWorkspaceChart.heading' => 'Services Distribution by Workspace',
    'Widgets.AdminServicesByWorkspaceChart.datasets.label' => 'Services',

    'Widgets.AdminUsersGrowthChart.heading' => 'Users Growth Over Time',
    'Widgets.AdminUsersGrowthChart.datasets.label' => 'New Users',

    'Widgets.AdminWorkspacesTrendChart.heading' => 'Workspaces Creation Trend',
    'Widgets.AdminWorkspacesTrendChart.datasets.label' => 'New Workspaces',

    'Widgets.SecretaryBookingsChart.heading' => 'Workspace Bookings Over Time',
    'Widgets.SecretaryBookingsChart.datasets.label' => 'Bookings',

    'Widgets.SecretaryConversationsActivityChart.heading' => 'Conversations Activity Over Time',
    'Widgets.SecretaryConversationsActivityChart.datasets.label' => 'New Conversations',

    'Widgets.SecretaryServiceRequestsByStatusChart.heading' => 'Workspace Service Requests by Status',
    'Widgets.SecretaryServiceRequestsByStatusChart.datasets.label' => 'Service Requests',

    'Widgets.SecretaryServicesOverTimeChart.heading' => 'Services Over Time',
    'Widgets.SecretaryServicesOverTimeChart.datasets.label' => 'New Services',
];
