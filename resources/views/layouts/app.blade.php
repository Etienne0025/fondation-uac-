<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

        @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
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
    </body>
</html>