// Demander permission notifications navigateur
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Enregistrer le service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker enregistré.'))
        .catch(err => console.error('Erreur Service Worker:', err));
}

// Afficher bannière en haut à droite
window.afficherBanniere = function(titre, message, type) {
    const couleur = type === 'rappel' ? '#15803d' : '#dc2626';
    const icone   = type === 'rappel' ? '🔔' : '⚠️';

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
        </div>
        <button onclick="this.parentElement.remove()"
            style="background:none;border:none;color:white;font-size:18px;cursor:pointer;opacity:0.7;padding:0;margin-left:8px">
            ✕
        </button>
    `;
    document.body.appendChild(banniere);

    // Notification navigateur
    if (Notification.permission === 'granted') {
        new Notification(titre, {
            body: message,
            icon: '/image/logo-fuac.jpg',
        });
    }

    // Disparaître après 6 secondes
    setTimeout(() => {
        if (banniere.parentElement) {
            banniere.style.transition = 'opacity 0.5s';
            banniere.style.opacity = '0';
            setTimeout(() => banniere.remove(), 500);
        }
    }, 6000);
}

// Mettre à jour le badge (dropdown + sidebar) et le dropdown
function mettreAJourBadge(notifications) {
    const badge    = document.getElementById('notif-badge');
    const liste    = document.getElementById('notif-liste');
    const badgeSidebar = document.getElementById('sidebar-notif-badge');

    const count = notifications.length;

    // Badge du sidebar (toutes notifications confondues : activites + reseaux)
    if (badgeSidebar) {
        if (count > 0) {
            badgeSidebar.textContent = count > 99 ? '99+' : count;
            badgeSidebar.classList.remove('hidden');
        } else {
            badgeSidebar.classList.add('hidden');
        }
    }

    if (!badge || !liste) return;

    // Badge du dropdown (menu cloche)
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    // Liste dropdown
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

// Stock notifications non lues (accumule entre chaque sondage)
let notificationsNonLues = [];

// Marquer toutes comme lues
window.marquerToutesLues = async function() {
    await fetch('/notifications/lire', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        }
    });

    // Vide le tableau local, sinon le prochain sondage rafficherait les anciennes
    notificationsNonLues = [];

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

        // Gestion Activités
        if (data.activites && data.activites.length > 0) {
            data.activites.forEach(notif => {
                window.afficherBanniere(notif.activite.titre, 'Rappel activité', notif.type);
                notificationsNonLues.push(notif);
            });
            nouvelles = true;
            // Recharger la liste si nécessaire
            if (typeof rafraichirListe === 'function') rafraichirListe();
        }

        // Gestion Réseaux Sociaux (nouveau système)
        if (data.reseaux && data.reseaux.length > 0) {
            data.reseaux.forEach(alerte => {
                window.afficherBanniere(
                    alerte.type.toUpperCase() + ' - Nouveau message',
                    'De: ' + alerte.expediteur,
                    'alerte'
                );
                notificationsNonLues.push(alerte);
            });
            nouvelles = true;
        }

        // Met a jour le badge et le dropdown uniquement s'il y a du nouveau
        if (nouvelles) {
            mettreAJourBadge(notificationsNonLues);
        }
    } catch (error) {
        console.error('Erreur Polling:', error);
    }
}

// Lancer le polling
setInterval(verifierNotifications, 10000);

// Fonction de suppression multiple via formulaire
window.supprimerSelectionnes = async function(formElement) {
    const formData = new FormData(formElement);
    const response = await fetch(formElement.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });

    if (response.ok) {
        location.reload(); // Recharger pour nettoyer l'interface
    }
};