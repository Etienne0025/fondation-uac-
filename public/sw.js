// ============================================
// public/sw.js
// ============================================

self.addEventListener('push', function (event) {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'FUAC Agenda', body: event.data ? event.data.text() : '' };
    }

    const titre = data.title || 'FUAC Agenda';

    const options = {
        body: data.body || '',
        icon: data.icon || '/image/logo-fuac.jpg',
        badge: '/image/logo-fuac.jpg',
        data: data.data || {},
        vibrate: [100, 50, 100],
        // La notification reste affichée tant que l'utilisateur n'a pas
        // cliqué dessus ou sur l'un des boutons — elle ne disparaît pas
        // toute seule et n'est pas balayable comme une notification normale.
        requireInteraction: true,
    };

    // Boutons "ne plus rappeler" / "rappeler", uniquement pour les rappels d'activité
    if (data.data && data.data.type === 'rappel' && data.data.notification_id) {
        options.actions = [
            { action: 'arreter', title: 'Ne plus rappeler' },
            { action: 'rappeler', title: 'Rappeler' },
        ];
    }

    event.waitUntil(self.registration.showNotification(titre, options));
});

self.addEventListener('notificationclick', function (event) {
    const notifData = event.notification.data || {};
    const url = notifData.url || '/responsable';

    // Clic sur un des deux boutons d'action
    if (event.action === 'arreter' || event.action === 'rappeler') {
        event.notification.close();

        const endpoint = event.action === 'arreter'
            ? `/notifications/${notifData.notification_id}/arreter-rappel`
            : `/notifications/${notifData.notification_id}/relancer-rappel`;

        event.waitUntil(
            getCsrfToken().then((token) =>
                fetch(endpoint, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': token,
                    },
                })
            )
        );
        return;
    }

    // Clic sur la notification elle-même : ouvrir/focus l'app
    event.notification.close();

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Lit le cookie XSRF-TOKEN posé par Laravel pour sécuriser les requêtes POST
// envoyées depuis le service worker (celui-ci n'a pas accès au DOM/meta tag).
function getCsrfToken() {
    return self.cookieStore
        ? self.cookieStore.get('XSRF-TOKEN').then((c) => decodeURIComponent(c ? c.value : ''))
        : Promise.resolve('');
}