// Demander permission notifications navigateur
// (toujours nécessaire : c'est cette permission qui autorise aussi les vraies
// notifications push envoyées via le service worker)
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Enregistrer le service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker enregistré.'))
        .catch(err => console.error('Erreur Service Worker:', err));
}

// Notifications en attente d'acquittement (pas encore cliquees sur OK)
let notificationsEnAttente = [];

function idUnique(prefixe, id) {
    return prefixe + '-' + id;
}

// Affiche une banniere persistante (dans la page) avec un bouton OK.
// La vraie notification systeme (OS/navigateur) n'est plus déclenchée ici :
// elle est envoyée par le serveur via WebPush (voir RappelActiviteNotification
// + public/sw.js), ce qui permet d'avoir le titre/date corrects et les boutons
// "ne plus rappeler" / "rappeler". Ce fichier ne gère que l'affichage in-page.
// idNotif : identifiant unique (ex: 'notif-12' ou 'alerte-7'), utilise pour eviter les doublons et gerer l'acquittement
window.afficherBanniere = function(titre, message, type, idNotif = null) {
    const couleur = type === 'rappel' ? '#15803d' : '#dc2626';
    const icone   = type === 'rappel' ? '🔔' : '⚠️';

    // Evite d'empiler deux fois la meme banniere si elle est deja affichee a l'ecran
    if (idNotif && document.querySelector(`[data-notif-id="${idNotif}"]`)) {
        return;
    }

    if (!document.getElementById('banniere-style')) {
        const style = document.createElement('style');
        style.id = 'banniere-style';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(110%); opacity: 0; }
                to   { transform: translateX(0);    opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }

    const banniere = document.createElement('div');
    if (idNotif) banniere.dataset.notifId = idNotif;
    banniere.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: ${couleur};
        color: white;
        padding: 14px 18px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        display: flex;
        align-items: flex-start;
        gap: 10px;
        max-width: 320px;
        font-family: sans-serif;
        animation: slideIn 0.3s ease-out;
    `;
    banniere.innerHTML = `
        <span style="font-size:18px;margin-top:2px">${icone}</span>
        <div style="flex:1">
            <p style="font-weight:600;font-size:14px;margin:0">${titre}</p>
            <p style="font-size:12px;opacity:0.9;margin:4px 0 0">${message}</p>
            <button class="banniere-ok-btn" style="margin-top:10px;background:rgba(255,255,255,0.2);border:none;color:white;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;cursor:pointer">
                OK
            </button>
        </div>
    `;
    document.body.appendChild(banniere);

    // Clic sur OK : ferme la banniere ET l'acquitte (elle ne reviendra plus)
    banniere.querySelector('.banniere-ok-btn').addEventListener('click', () => {
        banniere.style.transition = 'opacity 0.3s';
        banniere.style.opacity = '0';
        setTimeout(() => banniere.remove(), 300);

        if (idNotif) {
            notificationsEnAttente = notificationsEnAttente.filter(n => n.idNotif !== idNotif);
        }
    });

    // PAS de disparition automatique : la banniere reste tant que l'utilisateur ne clique pas OK
}

// Mettre à jour le badge et le dropdown
function mettreAJourBadge(notifications) {
    const badge    = document.getElementById('notif-badge');
    const liste    = document.getElementById('notif-liste');
    const badgeSidebar = document.getElementById('sidebar-notif-badge');

    const count = notifications.length;

    if (badgeSidebar) {
        if (count > 0) {
            badgeSidebar.textContent = count > 99 ? '99+' : count;
            badgeSidebar.classList.remove('hidden');
        } else {
            badgeSidebar.classList.add('hidden');
        }
    }

    if (!badge || !liste) return;

    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    if (count === 0) {
        liste.innerHTML = `
            <div class="px-4 py-8 text-center text-sm text-gray-400">
                Aucune nouvelle notification
            </div>
        `;
        return;
    }

    liste.innerHTML = notifications.map(notif => {
        const titre  = notif.activite?.titre || notif.expediteur || 'FUAC Agenda';
        const icone  = notif.type === 'rappel' ? '🔔' : '⚠️';
        const date   = new Date(notif.dateenvoi || notif.detecte_a).toLocaleString('fr-FR', {
            day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit'
        });
        const message = notif.type === 'rappel'
            ? 'Rappel : "' + titre + '" approche.'
            : notif.type === 'alerte'
                ? 'Alerte : "' + titre + '" non marquée comme faite.'
                : 'De: ' + (notif.expediteur || 'Inconnu');

        return `
            <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50"
                style="border-left: 3px solid ${notif.type === 'rappel' ? '#15803d' : '#dc2626'}">
                <span style="font-size:16px;margin-top:2px">${icone}</span>
                <div class="flex-1">
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0">${titre}</p>
                    <p style="font-size:11px;color:#6b7280;margin:2px 0 0">${message}</p>
                    <p style="font-size:10px;color:#9ca3af;margin:4px 0 0">${date}</p>
                </div>
            </div>
        `;
    }).join('');
}

// Toggle dropdown
window.toggleNotifications = function() {
    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Fermer dropdown si clic ailleurs
document.addEventListener('click', function(e) {
    const container = document.getElementById('notif-container');
    if (container && !container.contains(e.target)) {
        const dropdown = document.getElementById('notif-dropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }
});

// Marquer toutes comme lues
window.marquerToutesLues = async function() {
    await fetch('/notifications/lire', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        }
    });

    notificationsEnAttente = [];

    // Ferme aussi toutes les bannieres actuellement affichees a l'ecran
    document.querySelectorAll('[data-notif-id]').forEach(b => b.remove());

    const badge = document.getElementById('notif-badge');
    const liste = document.getElementById('notif-liste');
    const badgeSidebar = document.getElementById('sidebar-notif-badge');
    if (badge) badge.classList.add('hidden');
    if (badgeSidebar) badgeSidebar.classList.add('hidden');
    if (liste) liste.innerHTML = `
        <div class="px-4 py-8 text-center text-sm text-gray-400">
            Aucune nouvelle notification
        </div>
    `;
}

// Polling vérifiant toutes les 10 secondes
async function verifierNotifications() {
    try {
        const response = await fetch('/notifications/check', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        if (!response.ok) return;

        const data = await response.json();
        let nouvelles = false;

        if (data.activites && data.activites.length > 0) {
            data.activites.forEach(notif => {
                const id = idUnique('notif', notif.id);
                const dateActivite = notif.activite.date
                    ? new Date(notif.activite.date).toLocaleString('fr-FR', {
                          day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                      })
                    : '';
                const message = dateActivite ? `Prévu le ${dateActivite}` : 'Rappel activité';
                window.afficherBanniere(notif.activite.titre, message, notif.type, id);
                notificationsNonLues.push(notif);
                notificationsEnAttente.push({ idNotif: id, titre: notif.activite.titre, message, type: notif.type });
            });
            nouvelles = true;
            if (typeof rafraichirListe === 'function') rafraichirListe();
        }

        if (data.reseaux && data.reseaux.length > 0) {
            data.reseaux.forEach(alerte => {
                const id = idUnique('alerte', alerte.id);
                const titre = alerte.type.toUpperCase() + ' - Nouveau message';
                const message = 'De: ' + alerte.expediteur;
                window.afficherBanniere(titre, message, 'alerte', id);
                notificationsNonLues.push(alerte);
                notificationsEnAttente.push({ idNotif: id, titre, message, type: 'alerte' });
            });
            nouvelles = true;
        }

        if (nouvelles) {
            mettreAJourBadge(notificationsNonLues);
        }
    } catch (error) {
        console.error('Erreur Polling:', error);
    }
}

// Stock notifications non lues (accumule entre chaque sondage, pour le badge/dropdown)
let notificationsNonLues = [];

// Lancer le polling
setInterval(verifierNotifications, 10000);

// Reaffiche toutes les 5 minutes les notifications non acquittees (bouton OK jamais clique)
setInterval(() => {
    notificationsEnAttente.forEach(notif => {
        window.afficherBanniere(notif.titre, notif.message, notif.type, notif.idNotif);
    });
}, 5 * 60 * 1000);

// Fonction de suppression multiple via formulaire
window.supprimerSelectionnes = async function(formElement) {
    const formData = new FormData(formElement);
    const response = await fetch(formElement.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });

    if (response.ok) {
        location.reload();
    }
};