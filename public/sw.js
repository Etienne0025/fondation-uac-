self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    const title = data.titre || 'FUAC Agenda';
    const options = {
        body:    data.message || 'Nouvelle notification',
        icon:    '/image/logo-fuac.jpg',
        badge:   '/image/logo-fuac.jpg',
        vibrate: [200, 100, 200],
        data:    { url: data.url || '/responsable' },
        actions: [
            { action: 'voir',   title: '👁 Voir'    },
            { action: 'fermer', title: '✕ Fermer'  },
        ]
    };
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.action === 'voir') {
        clients.openWindow(event.notification.data.url);
    }
});