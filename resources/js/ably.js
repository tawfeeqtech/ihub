import Echo from "laravel-echo";
import * as Ably from "ably";

window.Ably = Ably;

window.Echo = new Echo({
    broadcaster: "ably",
    key: import.meta.env.VITE_ABLY_PUBLIC_KEY,
    authEndpoint: "broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content"),
        },
    },
});
