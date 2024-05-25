import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.hostname + ":" + window.laravel_echo_port,
    auth: {
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Authorization': window.user_type
        }
    },
});
