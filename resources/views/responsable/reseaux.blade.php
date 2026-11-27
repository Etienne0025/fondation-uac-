<x-app-layout>
<div class="flex h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Réseaux sociaux</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">


            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <div class="grid grid-cols-2 gap-6">

                {{-- Gmail --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                <path d="M20 4H4C2.9 4 2 4.9 2 6v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="#EA4335"/>
                                <path d="M20 4H4L12 13L20 4z" fill="#FBBC05"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Gmail</p>
                            <p class="text-xs text-gray-400">Alertes messages non lus</p>
                        </div>
                        @if($gmail && $gmail->actif)
                        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                            ✓ Connecté
                        </span>
                        @else
                        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                            Non connecté
                        </span>
                        @endif
                    </div>

                    @if($gmail && $gmail->actif)

                        {{-- Compte connecté --}}
                        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-600">
                            <p><span class="font-medium">Compte :</span> {{ $gmail->email_compte }}</p>
                        </div>

                        {{-- Délai d'alerte --}}
                        <form method="POST" action="{{ route('reseau.delai') }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="nomcompte" value="gmail">
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Délai d'alerte sans réponse
                            </label>
                            <div class="flex gap-2">
                                <input type="number" name="delai_alerte" min="1"
                                    value="{{ $gmail->delai_alerte }}"
                                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                                <select name="delai_unite"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white focus:ring-green-600 focus:border-green-600">
                                    <option value="minutes" {{ ($gmail->delai_unite ?? 'minutes') == 'minutes' ? 'selected' : '' }}>Minute(s)</option>
                                    <option value="heures"  {{ ($gmail->delai_unite ?? '') == 'heures'  ? 'selected' : '' }}>Heure(s)</option>
                                    <option value="jours"   {{ ($gmail->delai_unite ?? '') == 'jours'   ? 'selected' : '' }}>Jour(s)</option>
                                </select>
                                <button type="submit"
                                    class="bg-green-700 text-white text-xs px-4 py-2 rounded-lg hover:bg-green-800 transition">
                                    Sauvegarder
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Alerte si messages non lus depuis plus de
                                {{ $gmail->delai_alerte }} {{ $gmail->delai_unite ?? 'minute(s)' }}.
                            </p>
                        </form>

                        {{-- Déconnecter --}}
                        <form method="POST" action="{{ route('reseau.deconnecter', $gmail) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Déconnecter Gmail ?')"
                                class="text-xs border border-red-200 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                                Déconnecter Gmail
                            </button>
                        </form>

                    @else

                        {{-- Connecter Gmail --}}
                        <p class="text-xs text-gray-500 mb-4">
                            Connectez votre compte Gmail pour recevoir des alertes quand vous avez des messages non lus.
                        </p>

                        {{-- Délai avant connexion --}}
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Délai d'alerte (après connexion)
                            </label>
                            <div class="flex gap-2">
                                <input type="number" name="delai_alerte_gmail" min="1" value="30" id="delai_gmail"
                                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
                                <select name="delai_unite_gmail" id="unite_gmail"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <option value="minutes">Minute(s)</option>
                                    <option value="heures">Heure(s)</option>
                                    <option value="jours">Jour(s)</option>
                                </select>
                            </div>
                        </div>

                        <a href="{{ route('reseau.gmail.connecter') }}"
                            class="flex items-center justify-center gap-2 w-full border border-gray-300 rounded-lg py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Connecter avec Google
                        </a>

                    @endif
                </div>

                {{-- WhatsApp --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#25D366">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">WhatsApp</p>
                            <p class="text-xs text-gray-400">Alertes messages non lus</p>
                        </div>
                        @if($whatsapp && $whatsapp->actif)
                        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                            ✓ Connecté
                        </span>
                        @else
                        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                            Non connecté
                        </span>
                        @endif
                    </div>

                    @if($whatsapp && $whatsapp->actif)

                        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-600">
                            <p><span class="font-medium">Téléphone :</span> {{ $whatsapp->telephone_compte }}</p>
                        </div>

                        {{-- Délai d'alerte --}}
                        <form method="POST" action="{{ route('reseau.delai') }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="nomcompte" value="whatsapp">
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Délai d'alerte sans réponse
                            </label>
                            <div class="flex gap-2">
                                <input type="number" name="delai_alerte" min="1"
                                    value="{{ $whatsapp->delai_alerte }}"
                                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                                <select name="delai_unite"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white focus:ring-green-600 focus:border-green-600">
                                    <option value="minutes" {{ ($whatsapp->delai_unite ?? 'minutes') == 'minutes' ? 'selected' : '' }}>Minute(s)</option>
                                    <option value="heures"  {{ ($whatsapp->delai_unite ?? '') == 'heures'  ? 'selected' : '' }}>Heure(s)</option>
                                    <option value="jours"   {{ ($whatsapp->delai_unite ?? '') == 'jours'   ? 'selected' : '' }}>Jour(s)</option>
                                </select>
                                <button type="submit"
                                    class="bg-green-700 text-white text-xs px-4 py-2 rounded-lg hover:bg-green-800 transition">
                                    Sauvegarder
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Alerte si messages non lus depuis plus de
                                {{ $whatsapp->delai_alerte }} {{ $whatsapp->delai_unite ?? 'minute(s)' }}.
                            </p>
                        </form>

                        {{-- Déconnecter --}}
                        <form method="POST" action="{{ route('reseau.deconnecter', $whatsapp) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Déconnecter WhatsApp ?')"
                                class="text-xs border border-red-200 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                                Déconnecter WhatsApp
                            </button>
                        </form>

                    @else

                        <p class="text-xs text-gray-500 mb-3">
                            Entrez votre numéro WhatsApp Business pour recevoir des alertes.
                        </p>

                        <form method="POST" action="{{ route('reseau.whatsapp.connecter') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Numéro WhatsApp</label>
                                <input type="tel" name="telephone"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600"
                                    placeholder="+229 01 XX XX XX XX" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Délai d'alerte</label>
                                <div class="flex gap-2">
                                    <input type="number" name="delai_alerte" min="1" value="30"
                                        class="w-24 p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                                    <select name="delai_unite"
                                        class="flex-1 p-2.5 border border-gray-300 rounded-lg text-sm outline-none bg-white focus:ring-green-600 focus:border-green-600">
                                        <option value="minutes">Minute(s)</option>
                                        <option value="heures">Heure(s)</option>
                                        <option value="jours">Jour(s)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit"
                                class="flex items-center justify-center gap-2 w-full bg-green-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-green-700 transition">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="white">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Connecter WhatsApp
                            </button>
                        </form>

                    @endif
                </div>

                {{-- Messenger --}}
<div class="bg-white border border-gray-200 rounded-xl p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#0084FF">
                <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800">Messenger</p>
            <p class="text-xs text-gray-400">Alertes messages privés non traités</p>
        </div>
        @if($messenger && $messenger->actif)
        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
            ✓ Connecté
        </span>
        @else
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
            Non connecté
        </span>
        @endif
    </div>

    @if($messenger && $messenger->actif)

        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-600">
            <p><span class="font-medium">Page :</span> {{ $messenger->nom_page }}</p>
        </div>

        <form method="POST" action="{{ route('reseau.delai') }}" class="mb-4">
            @csrf
            <input type="hidden" name="nomcompte" value="messenger">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Délai d'alerte sans réponse
            </label>
            <div class="flex gap-2">
                <input type="number" name="delai_alerte" min="1"
                    value="{{ $messenger->delai_alerte }}"
                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                <select name="delai_unite"
                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white focus:ring-green-600 focus:border-green-600">
                    <option value="minutes" {{ ($messenger->delai_unite ?? 'minutes') == 'minutes' ? 'selected' : '' }}>Minute(s)</option>
                    <option value="heures"  {{ ($messenger->delai_unite ?? '') == 'heures'  ? 'selected' : '' }}>Heure(s)</option>
                    <option value="jours"   {{ ($messenger->delai_unite ?? '') == 'jours'   ? 'selected' : '' }}>Jour(s)</option>
                </select>
                <button type="submit"
                    class="bg-green-700 text-white text-xs px-4 py-2 rounded-lg hover:bg-green-800 transition">
                    Sauvegarder
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                Alerte si message non traité depuis plus de
                {{ $messenger->delai_alerte }} {{ $messenger->delai_unite ?? 'minute(s)' }}.
            </p>
        </form>

        <form method="POST" action="{{ route('reseau.deconnecter', $messenger) }}">
            @csrf @method('DELETE')
            <button type="submit"
                onclick="return confirm('Déconnecter Messenger ?')"
                class="text-xs border border-red-200 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                Déconnecter Messenger
            </button>
        </form>

    @else

        <p class="text-xs text-gray-500 mb-4">
            Connectez votre Page Facebook pour recevoir des alertes quand un message privé reste sans réponse.
        </p>

        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Délai d'alerte (après connexion)
            </label>
            <div class="flex gap-2">
                <input type="number" name="delai_alerte_messenger" min="1" value="30" id="delai_messenger"
                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
                <select name="delai_unite_messenger" id="unite_messenger"
                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                    <option value="minutes">Minute(s)</option>
                    <option value="heures">Heure(s)</option>
                    <option value="jours">Jour(s)</option>
                </select>
            </div>
        </div>

        <a href="{{ route('reseau.messenger.connecter') }}"
            class="flex items-center justify-center gap-2 w-full border border-gray-300 rounded-lg py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#0084FF">
                <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.652V24l4.088-2.242c1.09.301 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/>
            </svg>
            Connecter Messenger
        </a>

    @endif
</div>


{{-- Facebook (commentaires) --}}
<div class="bg-white border border-gray-200 rounded-xl p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800">Facebook</p>
            <p class="text-xs text-gray-400">Alertes commentaires non traités</p>
        </div>
        @if($facebook && $facebook->actif)
        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
            ✓ Connecté
        </span>
        @else
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
            Non connecté
        </span>
        @endif
    </div>

    @if($facebook && $facebook->actif)

        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-600">
            <p><span class="font-medium">Page :</span> {{ $facebook->nom_page }}</p>
        </div>

        <form method="POST" action="{{ route('reseau.delai') }}" class="mb-4">
            @csrf
            <input type="hidden" name="nomcompte" value="facebook">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Délai d'alerte sans réponse
            </label>
            <div class="flex gap-2">
                <input type="number" name="delai_alerte" min="1"
                    value="{{ $facebook->delai_alerte }}"
                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                <select name="delai_unite"
                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white focus:ring-green-600 focus:border-green-600">
                    <option value="minutes" {{ ($facebook->delai_unite ?? 'minutes') == 'minutes' ? 'selected' : '' }}>Minute(s)</option>
                    <option value="heures"  {{ ($facebook->delai_unite ?? '') == 'heures'  ? 'selected' : '' }}>Heure(s)</option>
                    <option value="jours"   {{ ($facebook->delai_unite ?? '') == 'jours'   ? 'selected' : '' }}>Jour(s)</option>
                </select>
                <button type="submit"
                    class="bg-green-700 text-white text-xs px-4 py-2 rounded-lg hover:bg-green-800 transition">
                    Sauvegarder
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                Alerte si commentaire non traité depuis plus de
                {{ $facebook->delai_alerte }} {{ $facebook->delai_unite ?? 'minute(s)' }}.
            </p>
        </form>

        <form method="POST" action="{{ route('reseau.deconnecter', $facebook) }}">
            @csrf @method('DELETE')
            <button type="submit"
                onclick="return confirm('Déconnecter Facebook ?')"
                class="text-xs border border-red-200 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                Déconnecter Facebook
            </button>
        </form>

    @else

        <p class="text-xs text-gray-500 mb-4">
            Connectez votre Page Facebook pour recevoir des alertes quand un commentaire reste sans réponse.
        </p>

        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Délai d'alerte (après connexion)
            </label>
            <div class="flex gap-2">
                <input type="number" name="delai_alerte_facebook" min="1" value="30" id="delai_facebook"
                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
                <select name="delai_unite_facebook" id="unite_facebook"
                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                    <option value="minutes">Minute(s)</option>
                    <option value="heures">Heure(s)</option>
                    <option value="jours">Jour(s)</option>
                </select>
            </div>
        </div>

        <a href="{{ route('reseau.facebook.connecter') }}"
            class="flex items-center justify-center gap-2 w-full border border-gray-300 rounded-lg py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Connecter Facebook
        </a>

    @endif
</div>

            </div>
        </main>
    </div>
</div>
</x-app-layout>