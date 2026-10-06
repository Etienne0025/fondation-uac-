<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

        @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#15803d">
        <link rel="icon" type="image/png" sizes="192x192" href="/image/icon-192.png">

        <!-- iOS (Safari ne lit pas le manifest.json pour l'installation) -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="FUAC Agenda">
        <link rel="apple-touch-icon" href="/image/icon-192.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
         <x-toast />
        <div class="min-h-screen bg-gray-100">

            {{-- Barre mobile avec bouton hamburger pour ouvrir le tiroir de la sidebar.
                 Visible uniquement en dessous du breakpoint md (donc sur téléphone/tablette étroite). --}}
            <div x-data class="md:hidden sticky top-0 z-20 flex items-center gap-3 bg-white border-b border-gray-200 px-4 py-3">
                <button type="button" @click="$store.sidebar.open = true"
                    class="p-1.5 text-gray-600 hover:text-green-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>
                <img src="/image/logo-fuac.jpg" alt="Logo" class="h-7 w-auto object-contain">
                <span class="font-semibold text-sm text-gray-800">Startup-Valley</span>
            </div>

            @isset($header)
                <header class="bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        <style>
            .btn-loading { opacity: .75; cursor: not-allowed; pointer-events: none; }
        </style>

        <script>
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.hasAttribute('data-no-loading')) return;

            // On attend la fin des autres écouteurs (ex : confirm()) pour vérifier que l'envoi a bien lieu
            setTimeout(() => {
                if (e.defaultPrevented) return;

                form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(btn => {
                    if (btn.dataset.loading) return;
                    btn.dataset.loading = '1';
                    btn.classList.add('btn-loading');
                    btn.setAttribute('aria-busy', 'true');

                    if (btn.tagName === 'BUTTON') {
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML =
                            '<span class="inline-flex items-center gap-2">' +
                            '<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                            '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                            '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>' +
                            '</svg>Chargement…</span>';
                    } else {
                        btn.dataset.originalValue = btn.value;
                        btn.value = 'Chargement…';
                    }
                });
            }, 0);
        });

        // Réinitialise les boutons si l'utilisateur revient en arrière (cache du navigateur)
        window.addEventListener('pageshow', function (e) {
            if (!e.persisted) return;
            document.querySelectorAll('[data-loading]').forEach(btn => {
                btn.classList.remove('btn-loading');
                btn.removeAttribute('aria-busy');
                if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
                if (btn.dataset.originalValue) btn.value = btn.dataset.originalValue;
                delete btn.dataset.loading;
            });
        });
        </script>
    </body>
</html>