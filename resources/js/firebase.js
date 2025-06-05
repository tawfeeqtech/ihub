// استيراد وظائف Firebase المطلوبة
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// إعدادات Firebase (احصل عليها من Firebase Console كما شرحنا سابقًا)
const firebaseConfig = {
    apiKey: "AIzaSyAAdGN2vfpua-u7Doa-Y2WzMwmHJgfFXnk",
    authDomain: "i-hup-420a6.firebaseapp.com",
    projectId: "i-hup-420a6",
    storageBucket: "i-hup-420a6.firebasestorage.app",
    messagingSenderId: "938462037862",
    appId: "1:938462037862:web:2f91c6757e3fe8502c6aa7",
    measurementId: "G-5KV10FYBE6",
};

// تهيئة التطبيق
const app = initializeApp(firebaseConfig);

// تهيئة خدمة المراسلة
const messaging = getMessaging(app);

// طلب إذن الإشعارات والحصول على Device Token
Notification.requestPermission().then((permission) => {
    console.log("permission: ", permission);

    if (permission === "granted") {
        getToken(messaging, {
            vapidKey:
                "BGiXumE6hOhRt8EXHU5gcUXnxPydCZ-uDggg0_s0FwUUiHAaQASNyxgBiDJVZFVoLTgKbzFpIVhMN7g788LCXvo",
        })
            .then((currentToken) => {
                if (currentToken) {
                    console.log("Device token:", currentToken);
                    // أرسل التوكن إلى السيرفر عبر API لحفظه
                    fetch("/api/v1/store-token", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify({ token: currentToken }),
                    });
                } else {
                    console.log("No registration token available.");
                }
            })
            .catch((err) => {
                console.log("An error occurred while retrieving token. ", err);
            });
    } else {
        console.log("Notification permission denied.");
    }
});

// استقبال الإشعارات أثناء عمل التطبيق
onMessage(messaging, (payload) => {
    console.log("Message received: ", payload);
    // هنا يمكنك عرض إشعار داخل لوحة التحكم (مثلاً باستخدام Toast أو Alert)
    alert(
        `إشعار جديد: ${payload.notification.title} - ${payload.notification.body}`
    );
});
