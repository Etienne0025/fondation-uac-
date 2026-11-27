import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import './notifications';
import './push-notifications';

window.Alpine = Alpine;
window.Swal = Swal;

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

// ---- Confirmation SweetAlert pour tout formulaire ayant un attribut data-confirm ----
document.addEventListener('submit', function (e) {
    const form = e.target;
    const message = form.dataset.confirm;

    // Pas de confirmation demandée, ou déjà confirmée : on laisse l'envoi se faire
    if (!message || form.dataset.confirmed === '1') return;

    e.preventDefault();

    Swal.fire({
        title: form.dataset.confirmTitle || 'Êtes-vous sûr ?',
        text: message,
        icon: form.dataset.confirmIcon || 'warning',
        showCancelButton: true,
        confirmButtonText: form.dataset.confirmButton || 'Oui, continuer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: form.dataset.confirmColor || '#15803d',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.requestSubmit();
        }
    });
});

// Si l'utilisateur revient en arrière, on réinitialise les confirmations
window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    document.querySelectorAll('form[data-confirmed]').forEach(f => delete f.dataset.confirmed);
});

// ---- Conversion automatique des anciens confirm() en SweetAlert ----
function convertirAnciensConfirm() {
    const re = /confirm\(\s*(['"])([\s\S]*?)\1\s*\)/;

    document.querySelectorAll('[onclick*="confirm("], [onsubmit*="confirm("]').forEach(el => {
        const attr = el.hasAttribute('onclick') ? 'onclick' : 'onsubmit';
        const m = el.getAttribute(attr).match(re);
        if (!m) return;

        const form = el.tagName === 'FORM' ? el : (el.form || el.closest('form'));
        if (!form) return;

        const message = m[2];
        const dangereux = /supprim|déconnect/i.test(message);

        form.dataset.confirm = message;
        if (dangereux) {
            form.dataset.confirmTitle = "Confirmer l'action";
            form.dataset.confirmButton = 'Oui, continuer';
            form.dataset.confirmColor = '#dc2626';
        } else {
            form.dataset.confirmIcon = 'question';
        }

        el.removeAttribute(attr);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', convertirAnciensConfirm);
} else {
    convertirAnciensConfirm();
}