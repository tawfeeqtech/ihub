<?php
return [
    'unread_message' => 'لديك رسالة غير مقروءة من :sender',
    'workspace_reservation_secretary' => "يوجد طلب جديد لحجز مساحة عمل :workspaceName من المستخدم :sender",
    'workspace_reservation_user' => "تم تحديث طلب حجز مساحة العمل لـ :space",

    'ServiceRequest_secretary' => "هناك طلب خدمة :serviceName من المستخدم  :sender",
    'ServiceRequest_user' => "طلب الخدمة :serviceName تم تأكيده",

    "sendServiceRequestNotification.serviceRequests" => "طلبات الخدمة",

    "notificationTitle.confirmed" => "تم تأكيد حجزك!",
    "notificationTitle.cancelled" => 'لقد تم إلغاء حجزك!',
    "notificationTitle.pending" => 'حالة الحجز الخاصة بك معلقة!',
    "notificationTitle.default" => 'تحديث حالة الحجز!',

    "notificationBody.confirmed" => 'تم تأكيد حجزك لمساحة العمل :workspaceName بنجاح، بإمكانك التحقق من اسم المستخدم وكلمة المرور.',
    "notificationBody.cancelled" => 'للأسف، تم إلغاء حجزك لمساحة العمل :workspaceName ',
    "notificationBody.pending" => 'حجزك لمساحة العمل :workspaceName لا يزال بانتظار التأكيد.',
    "notificationBody.default" => 'تم تحديث حالة حجزك لمساحة العمل :workspaceName ',

    'fcmResultErrorException' => 'فشل إرسال إشعار الدفع للمستخدم. السبب: ',
    'ErrorException' => 'لا يمكن إرسال إشعار الدفع: المستخدم غير موجود أو لا يوجد رمز جهاز FCM.',

    'EditBooking.notificationTitle' => 'تم حفظ التغييرات بنجاح!',
    'EditBooking.notificationBody' => 'تم تحديث حجز رقم المقعد: ',

    'EditBooking.catchErrorTitle' => 'خطأ في حفظ الحجز',
    'EditBooking.defaultWorkspaceName' => 'غير محددة',

];
