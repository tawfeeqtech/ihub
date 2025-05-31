import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});

// window.userConversationIds.forEach((id) => {
//     window.Echo.private(`conversations.${id}`).listen("MessageSent", (e) => {
//         console.log("New message received in convo", id, e);
//         Livewire.dispatch("refreshUnreadCount");
//     });
// });

// Echo.private("unread.count." + userId).listen(".unread.updated", () => {
//     // أعد طلب العداد من API
//     refreshUnreadCounter();
// });
