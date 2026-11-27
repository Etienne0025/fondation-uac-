<x-app-layout>
<div class="flex h-screen bg-gray-100">

     @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Mon profil</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">

            {{-- Grille parent avec même hauteur (suppression de items-start) --}}
            <div class="max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Informations personnelles --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col h-full justify-between">
                    <div>
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Informations personnelles</p>
                                <p class="text-xs text-gray-400">Votre nom, prénom et Votre adresse e-mail</p>
                            </div>
                        </div>
                        <div class="p-4">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col h-full justify-between">
                    <div>
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Mot de passe</p>
                                <p class="text-xs text-gray-400">Utilise un mot de passe long et unique</p>
                            </div>
                        </div>
                        <div class="p-4">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
</x-app-layout>