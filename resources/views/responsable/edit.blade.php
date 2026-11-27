<x-app-layout>
<div class="flex h-screen bg-gray-100">

     @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Modifier l'activité</h1>
            <a href="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.index') : route('responsable.index') }}"
                class="text-sm text-gray-500 hover:text-green-700">← Retour</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-2xl bg-white border border-gray-200 rounded-xl p-6">

                @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST"
                    action="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.update', $activite) : route('responsable.update', $activite) }}">
                    @csrf @method('PATCH')

                    {{-- Titre --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" name="titre" value="{{ old('titre', $activite->titre) }}"
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                            required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                            required>{{ old('description', $activite->description) }}</textarea>
                    </div>

                    {{-- Date/heure et Lieu --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date et heure</label>
                            <input type="datetime-local" name="date"
                                value="{{ old('date', $activite->date->format('Y-m-d\TH:i')) }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
                            <input type="text" name="lieu" value="{{ old('lieu', $activite->lieu) }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                required>
                        </div>
                    </div>

                    {{-- Droit d'accès --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Droit d'accès</label>
                        @php $droitActuel = old('droitacces', $activite->acces->droitacces ?? 'public'); @endphp
                        <select name="droitacces" id="droitacces-select" onchange="toggleMembres()"
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                            <option value="public"    {{ $droitActuel == 'public'    ? 'selected' : '' }}>Public</option>
                            <option value="prive"     {{ $droitActuel == 'prive'     ? 'selected' : '' }}>Privé</option>
                            <option value="restreint" {{ $droitActuel == 'restreint' ? 'selected' : '' }}>Restreint</option>
                        </select>
                    </div>

                    {{-- Membres autorisés (visible uniquement si Restreint) --}}
                    <div id="membres-container" class="mb-4 {{ $droitActuel == 'restreint' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Membres autorisés
                        </label>
                        <p class="text-xs text-gray-400 mb-2">
                            Sélectionne les personnes qui pourront voir cette activité et y participer.
                        </p>
                        <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto divide-y divide-gray-100">
                            @php $selectionnes = old('membres', $membresSelectionnes); @endphp
                            @forelse($utilisateurs as $u)
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
                            @php $rappelsAvant = old('rappels_avant', $activite->rappels_avant ?? []); @endphp
                            @forelse($rappelsAvant as $r)
                            <div class="flex gap-2 rappel-row">
                                <input type="number" name="rappels_avant_valeur[]" min="1" value="{{ $r['valeur'] }}"
                                    class="w-24 p-2 border border-gray-300 rounded-lg text-sm outline-none">
                                <select name="rappels_avant_unite[]"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <option value="secondes" {{ $r['unite'] == 'secondes' ? 'selected' : '' }}>Seconde(s) avant</option>
                                    <option value="minutes"  {{ $r['unite'] == 'minutes'  ? 'selected' : '' }}>Minute(s) avant</option>
                                    <option value="heures"   {{ $r['unite'] == 'heures'   ? 'selected' : '' }}>Heure(s) avant</option>
                                    <option value="jours"    {{ $r['unite'] == 'jours'    ? 'selected' : '' }}>Jour(s) avant</option>
                                </select>
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="text-red-500 hover:text-red-700 px-2">✕</button>
                            </div>
                            @empty
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
                            @endforelse
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
                            💡 Première alerte + intervalle de répétition jusqu'au clic sur "Fait".
                        </p>
                        <div id="alertes-apres-container" class="space-y-3 mb-2">
                            @php $alertesApres = old('alertes_apres', $activite->alertes_apres ?? []); @endphp
                            @forelse($alertesApres as $a)
                            <div class="alerte-row border border-gray-200 rounded-lg p-3 bg-gray-50 space-y-2">
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs text-gray-500 w-28 flex-shrink-0">Première alerte</span>
                                    <input type="number" name="alertes_apres_valeur[]" min="1" value="{{ $a['valeur'] }}"
                                        class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <select name="alertes_apres_unite[]"
                                        class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                        <option value="secondes" {{ $a['unite'] == 'secondes' ? 'selected' : '' }}>Seconde(s) après</option>
                                        <option value="minutes"  {{ $a['unite'] == 'minutes'  ? 'selected' : '' }}>Minute(s) après</option>
                                        <option value="heures"   {{ $a['unite'] == 'heures'   ? 'selected' : '' }}>Heure(s) après</option>
                                        <option value="jours"    {{ $a['unite'] == 'jours'    ? 'selected' : '' }}>Jour(s) après</option>
                                    </select>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs text-gray-500 w-28 flex-shrink-0">Répéter toutes les</span>
                                    <input type="number" name="alertes_intervalle_valeur[]" min="1" value="{{ $a['intervalle_valeur'] ?? 1 }}"
                                        class="w-20 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                    <select name="alertes_intervalle_unite[]"
                                        class="flex-1 p-2 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                        <option value="secondes" {{ ($a['intervalle_unite'] ?? '') == 'secondes' ? 'selected' : '' }}>Seconde(s)</option>
                                        <option value="minutes"  {{ ($a['intervalle_unite'] ?? '') == 'minutes'  ? 'selected' : '' }}>Minute(s)</option>
                                        <option value="heures"   {{ ($a['intervalle_unite'] ?? 'heures') == 'heures' ? 'selected' : '' }}>Heure(s)</option>
                                        <option value="jours"    {{ ($a['intervalle_unite'] ?? '') == 'jours'    ? 'selected' : '' }}>Jour(s)</option>
                                    </select>
                                    <button type="button" onclick="this.closest('.alerte-row').remove()"
                                        class="text-red-500 hover:text-red-700 px-2 text-lg">✕</button>
                                </div>
                            </div>
                            @empty
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
                            @endforelse
                        </div>
                        <button type="button" onclick="ajouterAlerte()"
                            class="text-xs text-amber-700 font-medium hover:underline">+ Ajouter une alerte</button>
                    </div>
                    --}}

                    {{-- Boutons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Mettre à jour
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