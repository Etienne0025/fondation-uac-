import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster:       'reverb',
    key:               import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:            'localhost',
    wsPort:            8080,
    wssPort:           8080,
    forceTLS:          false,
    disableStats:      true,
    enabledTransports: ['ws'],
    authEndpoint:      '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]')?.content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
    withCredentials: true,
});