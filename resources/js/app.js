import './bootstrap';

import Alpine from 'alpinejs';
import './notifications';

window.Alpine = Alpine;

Alpine.start();

setInterval(() => {
    fetch('/api/notifications/check') // Créez une route simple qui retourne le nombre de notifications non lues
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                // Rafraîchit automatiquement la page si une nouvelle alerte est détectée
                window.location.reload(); 
            }
        });
}, 10000); // 10 secondes