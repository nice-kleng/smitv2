import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    host: import.meta.env.VITE_PUSHER_HOST,
    port: import.meta.env.VITE_PUSHER_PORT,
    scheme: import.meta.env.VITE_PUSHER_SCHEME,
    forceTLS: false
});

window.Echo.channel('ticket')
    .listen('.ticket.created', (e) => {
        var audio = new Audio('/notification.wav');
        audio.play();
        toastr.success(e.message, 'Notifikasi');
    });
