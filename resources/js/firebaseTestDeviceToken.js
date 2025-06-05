// استيراد وظائف Firebase المطلوبة
import { initializeApp } from "firebase/app";
import {
    getMessaging,
    isSupported,
    getToken,
    onMessage,
} from "firebase/messaging";

// إعدادات Firebase (احصل عليها من Firebase Console)
const firebaseConfig = {
    apiKey: "AIzaSyAAdGN2vfpua-u7Doa-Y2WzMwmHJgfFXnk",
    authDomain: "i-hup-420a6.firebaseapp.com",
    projectId: "i-hup-420a6",
    storageBucket: "i-hup-420a6.firebasestorage.app",
    messagingSenderId: "938462037862",
    appId: "1:938462037862:web:2f91c6757e3fe8502c6aa7",
    measurementId: "G-5KV10FYBE6",
};

// تهيئة تطبيق Firebase
const app = initializeApp(firebaseConfig);

// تحقق من دعم المتصفح لـ Firebase Messaging
isSupported().then((supported) => {
    if (!supported) {
        console.warn("Firebase Messaging is not supported in this browser.");
        return;
    }

    // تهيئة خدمة المراسلة
    const messaging = getMessaging(app);

    // تسجيل Service Worker الخاص بـ Firebase Messaging
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker
            .register("/firebase-messaging-sw.js")
            .then((registration) => {
                console.log(
                    "Service Worker registered with scope:",
                    registration.scope
                );
                // طلب إذن الإشعارات من المستخدم
                return Notification.requestPermission();
            })
            .then((permission) => {
                if (permission !== "granted") {
                    console.log("Notification permission denied.");
                    throw new Error("Notification permission denied");
                }

                // تحقق من وجود توكن محفوظ في localStorage
                let savedToken = localStorage.getItem("device_token");

                if (savedToken) {
                    console.log("Using saved Device Token:", savedToken);
                    // يمكنك إرسال التوكن للسيرفر هنا إذا أردت تحديثه
                    return savedToken;
                } else {
                    // اطلب توكن جديد من Firebase
                    return getToken(messaging, {
                        vapidKey:
                            "BGiXumE6hOhRt8EXHU5gcUXnxPydCZ-uDggg0_s0FwUUiHAaQASNyxgBiDJVZFVoLTgKbzFpIVhMN7g788LCXvo",
                        // serviceWorkerRegistration: registration, // لم تستخدمها بسبب مشاكل
                    }).then((newToken) => {
                        if (newToken) {
                            localStorage.setItem("device_token", newToken);
                            console.log("New Device Token:", newToken);
                            return newToken;
                        } else {
                            console.log("No registration token available.");
                            return null;
                        }
                    });
                }
            })
            .then((token) => {
                if (token) {
                    console.log("Device Token:", token);
                    fetch("/store-token", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify({ token }),
                    });
                    // أرسل هذا التوكن للسيرفر لحفظه في قاعدة البيانات
                } else {
                    console.log("No registration token available.");
                }
            })
            .catch((err) => {
                console.error("Error getting token: ", err);
            });

        // استقبال الرسائل أثناء فتح الصفحة (foreground)
        onMessage(messaging, (payload) => {
            console.log("Message received in foreground: ", payload);
            // هنا يمكنك عرض إشعار مخصص داخل التطبيق
        });
    } else {
        console.error("Service workers are not supported in this browser.");
    }
});

// شغال تمام ولكن فيه تداخل في الكود

// // استيراد وظائف Firebase المطلوبة
// import { initializeApp } from "firebase/app";
// import {
//     getMessaging,
//     isSupported,
//     getToken,
//     onMessage,
// } from "firebase/messaging";

// isSupported().then((supported) => {
//     if (supported) {
//         const messaging = getMessaging();
//         // تابع تهيئة Firebase Messaging
//     } else {
//         console.warn("Firebase Messaging is not supported in this browser.");
//     }
// });

// // إعدادات Firebase (احصل عليها من Firebase Console كما شرحنا سابقًا)

// // // تهيئة التطبيق
// const app = initializeApp(firebaseConfig);

// if ("serviceWorker" in navigator) {
//     navigator.serviceWorker
//         .register("/firebase-messaging-sw.js")
//         .then((registration) => {
//             console.log(
//                 "Service Worker registered with scope:",
//                 registration.scope
//             );
//         })
//         .catch((err) => {
//             console.error("Service Worker registration failed:", err);
//         });
// }
// console.log("after serviceWorker");

// // // تهيئة خدمة المراسلة
// // const messaging = getMessaging(app);

// const messaging = getMessaging();

// Notification.requestPermission().then((permission) => {
//     if (permission === "granted") {
//         getToken(messaging, {
//             vapidKey:
//                 "BGiXumE6hOhRt8EXHU5gcUXnxPydCZ-uDggg0_s0FwUUiHAaQASNyxgBiDJVZFVoLTgKbzFpIVhMN7g788LCXvo",
//         })
//             .then((currentToken) => {
//                 if (currentToken) {
//                     console.log("Device Token:", currentToken);
//                     // أرسل هذا التوكن للسيرفر لحفظه في قاعدة البيانات
//                 } else {
//                     console.log("No registration token available.");
//                 }
//             })
//             .catch((err) => {
//                 console.log("Error getting token: ", err);
//             });
//     } else {
//         console.log("Notification permission denied.");
//     }
// });
