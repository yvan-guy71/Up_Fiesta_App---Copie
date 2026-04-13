import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    encrypted: false,
});

const userId = document.head.querySelector('meta[name="user-id"]')?.content;

if (userId) {
    window.Echo.private('users.' + userId)
        .listen('NotificationPushed', (e) => {
            // Reload or update notification dropdown
            if (window.refreshNotificationsDropdown) {
                window.refreshNotificationsDropdown();
            } else {
                window.location.reload();
            }
        });
}
