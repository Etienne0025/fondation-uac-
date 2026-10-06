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

// ---- Installation PWA ----
let promptInstallDiffere = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    promptInstallDiffere = event;

    const bouton = document.getElementById('installer-app-btn');
    if (bouton) bouton.classList.remove('hidden');
});

window.installerApplication = async function () {
    if (!promptInstallDiffere) return;

    promptInstallDiffere.prompt();
    const resultat = await promptInstallDiffere.userChoice;

    if (resultat.outcome === 'accepted') {
        document.getElementById('installer-app-btn')?.classList.add('hidden');
    }
    promptInstallDiffere = null;
};

window.addEventListener('appinstalled', () => {
    document.getElementById('installer-app-btn')?.classList.add('hidden');
});