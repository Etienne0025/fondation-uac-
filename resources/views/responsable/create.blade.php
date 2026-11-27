<x-app-layout>
<div class="flex h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Nouvelle activité</h1>
            <a href="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.index') : route('responsable.index') }}"
                class="text-sm text-gray-500 hover:text-green-700">← Retour</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-2xl bg-white border border-gray-200 rounded-xl p-6">

                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-800">Informations de l'activité</h2>
                    <p class="text-xs text-gray-400 mt-1">Remplissez les champs ci-dessous pour créer une activité.</p>
                </div>

                @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST"
                    action="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.store') : route('responsable.store') }}">
                    @csrf

                   {{-- Titre --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
    <div class="relative">
        <input type="text" name="titre" id="titre" value="{{ old('titre') }}"
            class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
            placeholder="Titre de l'activité" required>
        <button type="button" onclick="dicter('titre', this)"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700 transition"
            title="Dicter le titre">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
            </svg>
        </button>
    </div>
</div>

                   {{-- Description --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <div class="relative">
        <textarea name="description" id="description" rows="3"
            class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
            placeholder="Décrivez l'activité..." required>{{ old('description') }}</textarea>
        <button type="button" onclick="dicter('description', this)"
            class="absolute right-2 top-2 text-gray-400 hover:text-green-700 transition"
            title="Dicter la description">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
            </svg>
        </button>
    </div>
</div>

                    {{-- Date/heure et Lieu --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date et heure</label>
                            <input type="datetime-local" name="date" value="{{ old('date') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                required>
                        </div>
                       <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
    <div class="relative">
        <input type="text" name="lieu" id="lieu" value="{{ old('lieu') }}"
            class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
            placeholder="Ex : Salle A, FUAC" required>
        <button type="button" onclick="dicter('lieu', this)"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700 transition"
            title="Dicter le lieu">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
            </svg>
        </button>
    </div>
</div>
                    </div>

                    {{-- Droit d'accès --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Droit d'accès</label>
                        <select name="droitacces" id="droitacces-select" onchange="toggleMembres()"
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                            <option value="public"    {{ old('droitacces', 'public') == 'public'    ? 'selected' : '' }}>Public</option>
                            <option value="prive"     {{ old('droitacces') == 'prive'     ? 'selected' : '' }}>Privé</option>
                            <option value="restreint" {{ old('droitacces') == 'restreint' ? 'selected' : '' }}>Restreint</option>
                        </select>
                    </div>

                    {{-- Membres autorisés (visible uniquement si Restreint) --}}
                    <div id="membres-container" class="mb-4 {{ old('droitacces') == 'restreint' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Membres autorisés
                        </label>
                        <p class="text-xs text-gray-400 mb-2">
                            Sélectionne les personnes qui pourront voir cette activité et y participer.
                        </p>
                        <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto divide-y divide-gray-100">
                            @forelse($utilisateurs as $u)
                            @php $selectionnes = old('membres', []); @endphp
                            <label class="flex items-center gap-2.5 px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="membres[]" value="{{ $u->id }}"
                                    {{ in_array($u->id, $selectionnes) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-gray-700">{{ $u->nom }} {{ $u->prenom }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $u->poste ?? $u->role }}</span>
                            </label>
                            @empty
                            <p class="px-3 py-4 text-sm text-gray-400 text-center">Aucun membre actif disponible.</p>
                            @endforelse
                        </div>
                        @error('membres')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rappels avant --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rappels avant l'événement</label>
                        <div id="rappels-avant-container" class="space-y-2 mb-2">
                            <div class="flex gap-2 rappel-row">
                                <input type="number" name="rappels_avant_valeur[]" min="1" value="24"
                                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
                                <select name="rappels_avant_unite[]"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <option value="secondes">Seconde(s) avant</option>
                                    <option value="minutes">Minute(s) avant</option>
                                    <option value="heures" selected>Heure(s) avant</option>
                                    <option value="jours">Jour(s) avant</option>
                                </select>
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="text-red-500 hover:text-red-700 px-2">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="ajouterRappel()"
                            class="text-xs text-green-700 font-medium hover:underline">+ Ajouter un rappel</button>
                    </div>

                    {{--
                        Alertes après — DÉSACTIVÉES TEMPORAIREMENT (envoi coupé côté EnvoyerNotifications.php).
                        Section masquée pour ne pas laisser l'utilisateur configurer une alerte qui ne partira pas.
                        Pour réactiver : sortir ce bloc du commentaire Blade ci-dessous.

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alerter si l'événement n'est pas marqué comme fait
                        </label>
                        <p class="text-xs text-gray-400 mb-3">
                            💡 Définissez la première alerte et l'intervalle de répétition.
                        </p>
                        <div id="alertes-apres-container" class="space-y-3 mb-2">
                            <div class="alerte-row border border-gray-200 rounded-lg p-3 bg-gray-50 space-y-2">
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs text-gray-500 w-28 flex-shrink-0">Première alerte</span>
                                    <input type="number" name="alertes_apres_valeur[]" min="1" value="1"
                                        class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <select name="alertes_apres_unite[]"
                                        class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                        <option value="secondes">Seconde(s) après</option>
                                        <option value="minutes">Minute(s) après</option>
                                        <option value="heures" selected>Heure(s) après</option>
                                        <option value="jours">Jour(s) après</option>
                                    </select>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs text-gray-500 w-28 flex-shrink-0">Répéter toutes les</span>
                                    <input type="number" name="alertes_intervalle_valeur[]" min="1" value="1"
                                        class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <select name="alertes_intervalle_unite[]"
                                        class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                        <option value="secondes">Seconde(s)</option>
                                        <option value="minutes">Minute(s)</option>
                                        <option value="heures" selected>Heure(s)</option>
                                        <option value="jours">Jour(s)</option>
                                    </select>
                                    <button type="button" onclick="this.closest('.alerte-row').remove()"
                                        class="text-red-500 hover:text-red-700 px-2 text-lg">✕</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="ajouterAlerte()"
                            class="text-xs text-amber-700 font-medium hover:underline">+ Ajouter une alerte</button>
                    </div>
                    --}}

                    {{-- Boutons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Créer l'activité
                        </button>
                        <a href="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.index') : route('responsable.index') }}"
                            class="border border-gray-200 text-sm text-gray-500 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>

    function dicter(champId, boutonEl) {
    const SpeechRecognitionAPI = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognitionAPI) {
        alert("La dictée vocale n'est pas prise en charge par ce navigateur. Utilisez Chrome ou Edge.");
        return;
    }

    const champ = document.getElementById(champId);
    const recognition = new SpeechRecognitionAPI();
    recognition.lang = 'fr-FR';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    // Retour visuel pendant l'écoute
    boutonEl.classList.add('text-red-600', 'animate-pulse');
    boutonEl.disabled = true;

    recognition.onresult = function (event) {
        const texte = event.results[0][0].transcript;
        if (champ.value.trim().length > 0) {
            champ.value += ' ' + texte;
        } else {
            champ.value = texte;
        }
    };

    recognition.onerror = function (event) {
        if (event.error !== 'no-speech') {
            alert("Erreur de reconnaissance vocale : " + event.error);
        }
    };

    recognition.onend = function () {
        boutonEl.classList.remove('text-red-600', 'animate-pulse');
        boutonEl.disabled = false;
    };

    recognition.start();
}


function toggleMembres() {
    const select = document.getElementById('droitacces-select');
    const container = document.getElementById('membres-container');
    if (select.value === 'restreint') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function getOptionsAvant() {
    return `
        <option value="secondes">Seconde(s) avant</option>
        <option value="minutes">Minute(s) avant</option>
        <option value="heures" selected>Heure(s) avant</option>
        <option value="jours">Jour(s) avant</option>
    `;
}

function ajouterRappel() {
    const container = document.getElementById('rappels-avant-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 rappel-row';
    div.innerHTML = `
        <input type="number" name="rappels_avant_valeur[]" min="1" value="1"
            class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
        <select name="rappels_avant_unite[]"
            class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
            ${getOptionsAvant()}
        </select>
        <button type="button" onclick="this.parentElement.remove()"
            class="text-red-500 hover:text-red-700 px-2">✕</button>
    `;
    container.appendChild(div);
}

/*
    ajouterAlerte() désactivée temporairement avec la section "Alertes après" ci-dessus.
    Pour réactiver, sortir la fonction et son bouton du commentaire.

function getOptionsUnite(suffixe) {
    return `
        <option value="secondes">Seconde(s) ${suffixe}</option>
        <option value="minutes">Minute(s) ${suffixe}</option>
        <option value="heures" selected>Heure(s) ${suffixe}</option>
        <option value="jours">Jour(s) ${suffixe}</option>
    `;
}

function ajouterAlerte() {
    const container = document.getElementById('alertes-apres-container');
    const div = document.createElement('div');
    div.className = 'alerte-row border border-gray-200 rounded-lg p-3 bg-gray-50 space-y-2';
    div.innerHTML = `
        <div class="flex gap-2 items-center">
            <span class="text-xs text-gray-500 w-28 flex-shrink-0">Première alerte</span>
            <input type="number" name="alertes_apres_valeur[]" min="1" value="1"
                class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
            <select name="alertes_apres_unite[]"
                class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                ${getOptionsUnite('après')}
            </select>
        </div>
        <div class="flex gap-2 items-center">
            <span class="text-xs text-gray-500 w-28 flex-shrink-0">Répéter toutes les</span>
            <input type="number" name="alertes_intervalle_valeur[]" min="1" value="1"
                class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
            <select name="alertes_intervalle_unite[]"
                class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                ${getOptionsUnite('')}
            </select>
            <button type="button" onclick="this.closest('.alerte-row').remove()"
                class="text-red-500 hover:text-red-700 px-2 text-lg">✕</button>
        </div>
    `;
    container.appendChild(div);
}
*/
</script>

</x-app-layout>