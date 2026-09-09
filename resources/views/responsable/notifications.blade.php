<x-app-layout>
<div class="flex h-screen bg-gray-100">

    <x-sidebar-responsable />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-auto sm:h-14 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
            <div class="flex items-center">
                <x-mobile-menu-button />
                <h1 class="text-base sm:text-lg font-semibold text-gray-800">Notifications</h1>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full whitespace-nowrap">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-3 sm:p-6">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $notifications->total() }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Total activités</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M20 4H4C2.9 4 2 4.9 2 6v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="#EA4335"/>
                            <path d="M20 4H4L12 13L20 4z" fill="#FBBC05"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $alertesGmail->total() }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Alertes Gmail</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="#25D366">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $alertesWhatsapp->total() }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Alertes WhatsApp</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#0084FF" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $alertesMessenger->total() }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Alertes Messenger</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $alertesFacebook->total() }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Alertes Facebook</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 flex items-center gap-3 sm:gap-4">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $nonLues }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-400 truncate">Non lues</p>
                    </div>
                </div>
            </div>

            {{-- Contenu principal avec onglets --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                {{-- Onglets --}}
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button type="button" onclick="afficherOnglet('activites')" id="tab-activites"
                        class="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Activités
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                            {{ $notifications->total() }}
                        </span>
                    </button>

                    <button type="button" onclick="afficherOnglet('gmail')" id="tab-gmail"
                        class="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                            <path d="M20 4H4C2.9 4 2 4.9 2 6v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="#EA4335"/>
                            <path d="M20 4H4L12 13L20 4z" fill="#FBBC05"/>
                        </svg>
                        Gmail
                        @if($alertesGmail->total() > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                            {{ $alertesGmail->total() }}
                        </span>
                        @endif
                    </button>

                    <button type="button" onclick="afficherOnglet('whatsapp')" id="tab-whatsapp"
                        class="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#25D366">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                        @if($alertesWhatsapp->total() > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                            {{ $alertesWhatsapp->total() }}
                        </span>
                        @endif
                    </button>

                    <button type="button" onclick="afficherOnglet('messenger')" id="tab-messenger"
                        class="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="#0084FF" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
                        </svg>
                        Messenger
                        @if($alertesMessenger->total() > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $alertesMessenger->total() }}
                        </span>
                        @endif
                    </button>

                    <button type="button" onclick="afficherOnglet('facebook')" id="tab-facebook"
                        class="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-3 text-xs sm:text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                        @if($alertesFacebook->total() > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $alertesFacebook->total() }}
                        </span>
                        @endif
                    </button>
                </div>

                {{-- Filtres --}}
                <form method="GET" action="{{ route('responsable.notifications') }}" class="p-3 sm:p-4 border-b border-gray-100">
                    <input type="hidden" name="onglet" id="onglet-actif" value="{{ request('onglet', 'activites') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date début</label>
                            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date fin</label>
                            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                            <select name="lu" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                <option value="">Toutes</option>
                                <option value="non" {{ request('lu') == 'non' ? 'selected' : '' }}>Non lues</option>
                                <option value="oui" {{ request('lu') == 'oui' ? 'selected' : '' }}>Lues</option>
                            </select>
                        </div>
                    </div>

                    {{-- Filtre activités --}}
                    <div id="filtre-activites" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher activité</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Nom de l'activité..."
                                    class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select name="type" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                <option value="">Tous les types</option>
                                <option value="rappel" {{ request('type') == 'rappel' ? 'selected' : '' }}>Rappel</option>
                                <option value="alerte" {{ request('type') == 'alerte' ? 'selected' : '' }}>Alerte</option>
                            </select>
                        </div>
                    </div>

                    {{-- Filtre Gmail --}}
                    <div id="filtre-gmail" class="hidden mb-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher dans Gmail</label>
                        <div class="relative">
                            <input type="text" name="search_gmail" value="{{ request('search_gmail') }}"
                                placeholder="Expéditeur, sujet..."
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtre WhatsApp --}}
                    <div id="filtre-whatsapp" class="hidden mb-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher dans WhatsApp</label>
                        <div class="relative">
                            <input type="text" name="search_whatsapp" value="{{ request('search_whatsapp') }}"
                                placeholder="Numéro, message..."
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtre Messenger --}}
                    <div id="filtre-messenger" class="hidden mb-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher dans Messenger</label>
                        <div class="relative">
                            <input type="text" name="search_messenger" value="{{ request('search_messenger') }}"
                                placeholder="Expéditeur, message..."
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtre Facebook --}}
                    <div id="filtre-facebook" class="hidden mb-3">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher dans Facebook</label>
                        <div class="relative">
                            <input type="text" name="search_facebook" value="{{ request('search_facebook') }}"
                                placeholder="Expéditeur, commentaire..."
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                            Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'search_gmail', 'search_whatsapp', 'search_messenger', 'search_facebook', 'type', 'date_debut', 'date_fin', 'lu']))
                        <a href="{{ route('responsable.notifications', ['onglet' => request('onglet', 'activites')]) }}"
                            class="border border-gray-200 text-xs text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                            Réinitialiser
                        </a>
                        @endif
                        @if($nonLues > 0)
                        <form method="POST" action="{{ route('responsable.notifications.lire.tout') }}" class="w-full sm:w-auto sm:ml-auto">
                            @csrf
                            <button type="submit"
                                class="w-full sm:w-auto border border-green-200 text-xs text-green-700 px-4 py-2 rounded-lg hover:bg-green-50 transition">
                                Tout marquer comme lu
                            </button>
                        </form>
                        @endif
                    </div>
                </form>

                {{-- Contenu onglet Activités --}}
                <div id="contenu-activites">
                    <form id="form-activites" method="POST" action="{{ route('responsable.notifications.supprimer') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="activites">

                        {{-- Barre d'actions groupées --}}
                        <div class="flex items-center justify-between bg-gray-50 border-b border-gray-100 px-3 sm:px-5 py-2 gap-2">
                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="selectall-activites" onclick="toggleAll('activites', this)"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="hidden xs:inline">Tout sélectionner</span>
                                <span class="xs:hidden">Tout</span>
                            </label>
                            <button type="submit" id="btn-delete-activites" disabled
                                onclick="return confirm('Supprimer les notifications sélectionnées ?');"
                                class="flex items-center gap-1 text-xs bg-red-600 text-white px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-red-700 transition opacity-40 pointer-events-none flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Supprimer</span> (<span id="count-activites">0</span>)
                            </button>
                        </div>

                        @forelse($notifications as $notification)
                        @php
                            $typeColors = [
                                'rappel' => ['bg' => 'bg-green-50', 'border' => 'border-green-500', 'badge' => 'bg-green-100 text-green-700'],
                                'alerte' => ['bg' => 'bg-red-50',   'border' => 'border-red-500',   'badge' => 'bg-red-100 text-red-700'],
                            ];
                            $colors = $typeColors[$notification->type] ?? $typeColors['rappel'];
                        @endphp
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 px-3 sm:px-5 py-4 border-b border-gray-100
                            {{ !$notification->vue ? $colors['bg'] : '' }}
                            border-l-4 {{ !$notification->vue ? $colors['border'] : 'border-transparent' }}
                            hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3 sm:gap-4 w-full">
                                <input type="checkbox" name="ids[]" value="{{ $notification->id }}" onchange="updateToolbar('activites')"
                                    class="mt-2 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="mt-1 w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $notification->type === 'rappel' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    @if($notification->type === 'rappel')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $colors['badge'] }}">
                                            {{ ucfirst($notification->type) }}
                                        </span>
                                        @if(!$notification->vue)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $notification->activite->titre ?? 'Activité supprimée' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $notification->type === 'rappel' ? 'Rappel : votre activité approche.' : 'Alerte : activité non marquée comme faite.' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->dateenvoi->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto pl-11 sm:pl-0 flex-shrink-0">
                                @if(!$notification->vue)
                                <button type="submit" form="lire-{{ $notification->id }}" class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition">
                                    Lu
                                </button>
                                @endif
                                @if($notification->activite_id)
                                <a href="{{ route('responsable.edit', $notification->activite_id) }}"
                                    class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition">
                                    Voir
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 003.844.148m-3.844-.148a23.856 23.856 0 01-5.455-1.31 8.964 8.964 0 002.3-5.542m3.155 6.852a3 3 0 005.667 1.329m-9.105-8.39a9 9 0 0115.91-6.346M3.25 3.25l17.5 17.5" />
                            </svg>
                            <p class="text-sm text-gray-500">Aucune notification d'activité.</p>
                        </div>
                        @endforelse
                    </form>

                    {{-- Formulaires "Lu" séparés (en dehors du form de suppression pour éviter les formulaires imbriqués) --}}
                    @foreach($notifications as $notification)
                        @if(!$notification->vue)
                        <form id="lire-{{ $notification->id }}" method="POST" action="{{ route('responsable.notifications.lire', $notification) }}" class="hidden">
                            @csrf
                        </form>
                        @endif
                    @endforeach

                    <div class="p-4">{{ $notifications->links() }}</div>
                </div>

                {{-- Contenu onglet Gmail --}}
                <div id="contenu-gmail" class="hidden">
                    <form id="form-gmail" method="POST" action="{{ route('responsable.notifications.supprimer') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="gmail">

                        <div class="flex items-center justify-between bg-gray-50 border-b border-gray-100 px-3 sm:px-5 py-2 gap-2">
                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="selectall-gmail" onclick="toggleAll('gmail', this)"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="hidden xs:inline">Tout sélectionner</span>
                                <span class="xs:hidden">Tout</span>
                            </label>
                            <button type="submit" id="btn-delete-gmail" disabled
                                onclick="return confirm('Supprimer les alertes Gmail sélectionnées ?');"
                                class="flex items-center gap-1 text-xs bg-red-600 text-white px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-red-700 transition opacity-40 pointer-events-none flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Supprimer</span> (<span id="count-gmail">0</span>)
                            </button>
                        </div>

                        @forelse($alertesGmail as $alerte)
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 px-3 sm:px-5 py-4 border-b border-gray-100
                            {{ !$alerte->vue ? 'bg-red-50 border-l-4 border-red-400' : 'border-l-4 border-transparent' }}
                            hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3 sm:gap-4 w-full">
                                <input type="checkbox" name="ids[]" value="{{ $alerte->id }}" onchange="updateToolbar('gmail')"
                                    class="mt-2 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M20 4H4C2.9 4 2 4.9 2 6v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="#EA4335"/>
                                        <path d="M20 4H4L12 13L20 4z" fill="#FBBC05"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-700">Gmail</span>
                                        @if(!$alerte->vue)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Vous avez un message non lu de
                                        <span class="text-red-600">{{ $alerte->expediteur ?? 'Inconnu' }}</span>
                                    </p>
                                    @if($alerte->sujet)
                                    <p class="text-xs text-gray-500 mt-0.5">Sujet : {{ $alerte->sujet }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($alerte->detecte_a)->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
                                    </p>
                                </div>
                            </div>
                            <a href="https://mail.google.com" target="_blank"
                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition flex-shrink-0 ml-11 sm:ml-0">
                                Ouvrir Gmail
                            </a>
                        </div>
                        @empty
                        <div class="px-5 py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" />
                            </svg>
                            <p class="text-sm text-gray-500">Aucune alerte Gmail.</p>
                        </div>
                        @endforelse
                    </form>
                    <div class="p-4">{{ $alertesGmail->links() }}</div>
                </div>

                {{-- Contenu onglet WhatsApp --}}
                <div id="contenu-whatsapp" class="hidden">
                    <form id="form-whatsapp" method="POST" action="{{ route('responsable.notifications.supprimer') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="whatsapp">

                        <div class="flex items-center justify-between bg-gray-50 border-b border-gray-100 px-3 sm:px-5 py-2 gap-2">
                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="selectall-whatsapp" onclick="toggleAll('whatsapp', this)"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="hidden xs:inline">Tout sélectionner</span>
                                <span class="xs:hidden">Tout</span>
                            </label>
                            <button type="submit" id="btn-delete-whatsapp" disabled
                                onclick="return confirm('Supprimer les alertes WhatsApp sélectionnées ?');"
                                class="flex items-center gap-1 text-xs bg-red-600 text-white px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-red-700 transition opacity-40 pointer-events-none flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Supprimer</span> (<span id="count-whatsapp">0</span>)
                            </button>
                        </div>

                        @forelse($alertesWhatsapp as $alerte)
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 px-3 sm:px-5 py-4 border-b border-gray-100
                            {{ !$alerte->vue ? 'bg-green-50 border-l-4 border-green-400' : 'border-l-4 border-transparent' }}
                            hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3 sm:gap-4 w-full">
                                <input type="checkbox" name="ids[]" value="{{ $alerte->id }}" onchange="updateToolbar('whatsapp')"
                                    class="mt-2 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#25D366">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">WhatsApp</span>
                                        @if(!$alerte->vue)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Vous avez un message non lu de
                                        <span class="text-green-600">{{ $alerte->expediteur ?? 'Inconnu' }}</span>
                                    </p>
                                    @if($alerte->sujet)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $alerte->sujet }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($alerte->detecte_a)->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
                                    </p>
                                </div>
                            </div>
                            <a href="https://web.whatsapp.com" target="_blank"
                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition flex-shrink-0 ml-11 sm:ml-0">
                                Ouvrir WhatsApp
                            </a>
                        </div>
                        @empty
                        <div class="px-5 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Aucune alerte WhatsApp.</p>
                        </div>
                        @endforelse
                    </form>
                    <div class="p-4">{{ $alertesWhatsapp->links() }}</div>
                </div>

                {{-- Contenu onglet Messenger --}}
                <div id="contenu-messenger" class="hidden">
                    <form id="form-messenger" method="POST" action="{{ route('responsable.notifications.supprimer') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="messenger">

                        <div class="flex items-center justify-between bg-gray-50 border-b border-gray-100 px-3 sm:px-5 py-2 gap-2">
                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="selectall-messenger" onclick="toggleAll('messenger', this)"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="hidden xs:inline">Tout sélectionner</span>
                                <span class="xs:hidden">Tout</span>
                            </label>
                            <button type="submit" id="btn-delete-messenger" disabled
                                onclick="return confirm('Supprimer les alertes Messenger sélectionnées ?');"
                                class="flex items-center gap-1 text-xs bg-red-600 text-white px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-red-700 transition opacity-40 pointer-events-none flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Supprimer</span> (<span id="count-messenger">0</span>)
                            </button>
                        </div>

                        @forelse($alertesMessenger as $alerte)
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 px-3 sm:px-5 py-4 border-b border-gray-100
                            {{ !$alerte->vue ? 'bg-blue-50 border-l-4 border-blue-400' : 'border-l-4 border-transparent' }}
                            hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3 sm:gap-4 w-full">
                                <input type="checkbox" name="ids[]" value="{{ $alerte->id }}" onchange="updateToolbar('messenger')"
                                    class="mt-2 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="#0084FF" viewBox="0 0 24 24">
                                        <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Messenger</span>
                                        @if(!$alerte->vue)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Message non répondu de
                                        <span class="text-blue-600">{{ $alerte->expediteur ?? 'Inconnu' }}</span>
                                    </p>
                                    @if($alerte->sujet)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $alerte->sujet }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($alerte->detecte_a)->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
                                    </p>
                                </div>
                            </div>
                            <a href="https://www.messenger.com" target="_blank"
                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition flex-shrink-0 ml-11 sm:ml-0">
                                Ouvrir Messenger
                            </a>
                        </div>
                        @empty
                        <div class="px-5 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="#0084FF" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Aucune alerte Messenger.</p>
                        </div>
                        @endforelse
                    </form>
                    <div class="p-4">{{ $alertesMessenger->links() }}</div>
                </div>

                {{-- Contenu onglet Facebook --}}
                <div id="contenu-facebook" class="hidden">
                    <form id="form-facebook" method="POST" action="{{ route('responsable.notifications.supprimer') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="facebook">

                        <div class="flex items-center justify-between bg-gray-50 border-b border-gray-100 px-3 sm:px-5 py-2 gap-2">
                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="selectall-facebook" onclick="toggleAll('facebook', this)"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="hidden xs:inline">Tout sélectionner</span>
                                <span class="xs:hidden">Tout</span>
                            </label>
                            <button type="submit" id="btn-delete-facebook" disabled
                                onclick="return confirm('Supprimer les alertes Facebook sélectionnées ?');"
                                class="flex items-center gap-1 text-xs bg-red-600 text-white px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-red-700 transition opacity-40 pointer-events-none flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                <span class="hidden sm:inline">Supprimer</span> (<span id="count-facebook">0</span>)
                            </button>
                        </div>

                        @forelse($alertesFacebook as $alerte)
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 px-3 sm:px-5 py-4 border-b border-gray-100
                            {{ !$alerte->vue ? 'bg-blue-50 border-l-4 border-blue-400' : 'border-l-4 border-transparent' }}
                            hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3 sm:gap-4 w-full">
                                <input type="checkbox" name="ids[]" value="{{ $alerte->id }}" onchange="updateToolbar('facebook')"
                                    class="mt-2 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                <div class="mt-1 w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Facebook</span>
                                        @if(!$alerte->vue)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Commentaire non répondu de
                                        <span class="text-blue-600">{{ $alerte->expediteur ?? 'Inconnu' }}</span>
                                    </p>
                                    @if($alerte->sujet)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $alerte->sujet }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($alerte->detecte_a)->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
                                    </p>
                                </div>
                            </div>
                            <a href="https://facebook.com" target="_blank"
                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition flex-shrink-0 ml-11 sm:ml-0">
                                Ouvrir Facebook
                            </a>
                        </div>
                        @empty
                        <div class="px-5 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="#1877F2" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Aucune alerte Facebook.</p>
                        </div>
                        @endforelse
                    </form>
                    <div class="p-4">{{ $alertesFacebook->links() }}</div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
function afficherOnglet(onglet) {
    ['activites', 'gmail', 'whatsapp', 'messenger', 'facebook'].forEach(function(o) {
        document.getElementById('contenu-' + o).classList.add('hidden');
        var filtre = document.getElementById('filtre-' + o);
        if (filtre) filtre.classList.add('hidden');
        var tab = document.getElementById('tab-' + o);
        tab.classList.remove('text-green-700', 'border-green-700', 'bg-green-50');
        tab.classList.add('text-gray-500', 'border-transparent');
    });

    document.getElementById('contenu-' + onglet).classList.remove('hidden');
    var filtreActif = document.getElementById('filtre-' + onglet);
    if (filtreActif) filtreActif.classList.remove('hidden');

    var tabActif = document.getElementById('tab-' + onglet);
    tabActif.classList.remove('text-gray-500', 'border-transparent');
    tabActif.classList.add('text-green-700', 'border-green-700', 'bg-green-50');

    var input = document.getElementById('onglet-actif');
    if (input) input.value = onglet;
}

// Sélection groupée
function toggleAll(type, source) {
    var form = document.getElementById('form-' + type);
    var boxes = form.querySelectorAll('input[name="ids[]"]');
    boxes.forEach(function (b) { b.checked = source.checked; });
    updateToolbar(type);
}

function updateToolbar(type) {
    var form = document.getElementById('form-' + type);
    var boxes = form.querySelectorAll('input[name="ids[]"]');
    var checked = form.querySelectorAll('input[name="ids[]"]:checked');
    var count = checked.length;

    var countEl = document.getElementById('count-' + type);
    if (countEl) countEl.textContent = count;

    var btn = document.getElementById('btn-delete-' + type);
    if (btn) {
        btn.disabled = count === 0;
        btn.classList.toggle('opacity-40', count === 0);
        btn.classList.toggle('pointer-events-none', count === 0);
    }

    var selectAll = document.getElementById('selectall-' + type);
    if (selectAll) selectAll.checked = boxes.length > 0 && count === boxes.length;
}

document.addEventListener('DOMContentLoaded', function() {
    var ongletActif = '{{ request('onglet', 'activites') }}';
    afficherOnglet(ongletActif);

    ['activites', 'gmail', 'whatsapp', 'messenger', 'facebook'].forEach(function(type) {
        updateToolbar(type);
    });
});
</script>

</x-app-layout>