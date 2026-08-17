// Convertit la cle VAPID (base64 url-safe) en Uint8Array, format attendu par pushManager.subscribe
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function activerNotificationsPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert("Les notifications push ne sont pas supportées par ce navigateur.");
        return false;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        alert("Tu as refusé les notifications. Active-les dans les réglages du navigateur pour recevoir des alertes même hors ligne.");
        return false;
    }

    try {
        const registration = await navigator.serviceWorker.ready;
        const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]').content;

        let subscription = await registration.pushManager.getSubscription();
        if (!subscription) {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
            });
        }

        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(subscription),
        });

        localStorage.setItem('push_active', '1');
        return true;
    } catch (error) {
        console.error('Erreur activation push:', error);
        return false;
    }
}

// Bouton d'activation, a placer n'importe ou dans une page (ex: la cloche de notifications)
window.activerNotificationsPush = activerNotificationsPush;

// Propose automatiquement l'activation au premier chargement si jamais demande
document.addEventListener('DOMContentLoaded', () => {
    const bouton = document.getElementById('activer-push-btn');
    if (!bouton) return;

    if (localStorage.getItem('push_active') === '1') {
        bouton.classList.add('hidden');
        return;
    }

    bouton.addEventListener('click', async () => {
        const ok = await activerNotificationsPush();
        if (ok) {
            bouton.classList.add('hidden');
        }
    });
});