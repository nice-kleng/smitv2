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
    forceTLS: false,
    // encrypted: true,
    // auth: {
    //     headers: {
    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //     }
    // }
});

// window.Echo.channel('ticket')
//     .listen('.ticket.created', (e) => {
//         // Cek apakah browser mendukung notifikasi

//         if ('Notification' in window) {
//             if (Notification.permission === 'default') {
//                 Notification.requestPermission().then(permission => {
//                     if (permission === 'granted') {
//                         showNotification(e);
//                     }
//                 });
//             }
//             else if (Notification.permission === 'granted') {
//                 showNotification(e);
//             }
//         }
//         var audio = new Audio('/notification.wav');
//         audio.play();
//         toastr.success(e.message, 'Notifikasi');
//     });

// // Fungsi untuk menampilkan notifikasi
// function showNotification(e) {
//     new Notification('Pengaduan Baru', {
//         body: e.message || 'Pengaduan baru telah dibuat',
//         icon: '/favicon.ico', // Tambahkan icon default
//         tag: 'ticket-notification' // Menambahkan tag untuk menghindari duplikasi
//     });
// }
