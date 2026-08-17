import './bootstrap';

import Alpine from 'alpinejs';
import './notifications';
import './push-notifications';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: false,
    });
});

Alpine.start();