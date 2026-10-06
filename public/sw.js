// ============================================
// public/sw.js
// ============================================

const CACHE_NAME = 'fuac-agenda-v1';
const URLS_A_METTRE_EN_CACHE = [
    '/',
    '/manifest.json',
    '/image/logo-fuac.jpg',
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(URLS_A_METTRE_EN_CACHE);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (noms) {
            return Promise.all(
                noms.filter(function (nom) {
                    return nom !== CACHE_NAME;
                }).map(function (nom) {
                    return caches.delete(nom);
                })
            );
        })
    );
    self.clients.claim();
});

// Strategie simple : reseau d'abord, cache en secours (utile hors-ligne).
// Ne s'applique qu'aux requetes GET pour eviter d'interferer avec les
// formulaires POST/PATCH/DELETE de l'application.
self.addEventListener('fetch', function (event) {
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(function () {
            return caches.match(event.request);
        })
    );
});

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
        requireInteraction: true,
    };

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

function getCsrfToken() {
    return self.cookieStore
        ? self.cookieStore.get('XSRF-TOKEN').then((c) => decodeURIComponent(c ? c.value : ''))
        : Promise.resolve('');
}