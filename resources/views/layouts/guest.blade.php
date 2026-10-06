<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans text-gray-900 antialiased">
         <x-toast />
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            {{-- Logo affiché au-dessus de la card --}}
            <div class="mb-4">
                <img src="/image/logo-fuac.jpg" alt="Logo Startup Valley" class="h-20 w-auto object-contain">
            </div>

            <div class="w-full sm:max-w-2xl px-6 py-8 bg-white shadow-md overflow-visible sm:rounded-lg">
                {{ $slot }}
            </div>
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