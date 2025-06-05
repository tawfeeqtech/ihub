importScripts(
    "https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyAAdGN2vfpua-u7Doa-Y2WzMwmHJgfFXnk",
    authDomain: "i-hup-420a6.firebaseapp.com",
    projectId: "i-hup-420a6",
    storageBucket: "i-hup-420a6.firebasestorage.app",
    messagingSenderId: "938462037862",
    appId: "1:938462037862:web:2f91c6757e3fe8502c6aa7",
    measurementId: "G-5KV10FYBE6",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || "/firebase-logo.png",
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
