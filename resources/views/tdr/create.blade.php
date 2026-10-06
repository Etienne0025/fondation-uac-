<x-app-layout>
<div class="flex h-screen bg-gray-100 bg-cover bg-center bg-no-repeat"
     style="background-image: url('{{ asset('images/gb.jpg') }}');">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Publier un TDR</h1>
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
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="max-w-2xl bg-white border border-gray-200 rounded-xl p-6">

                <form method="POST" action="{{ route('tdr.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Choix explicite : projet existant ou nouveau --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Projet</label>
                       

                        <div id="nouveau-projet-container">
                            <div class="relative">
                                <input type="text" name="nouveau_projet" id="nouveau_projet" value="{{ old('nouveau_projet') }}"
                                    class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                    placeholder="Nom du nouveau projet, ex : Programme d'incubation 2026">
                                <button type="button" onclick="dicter('nouveau_projet', this)"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700 transition"
                                    title="Dicter le nom du projet">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="projet-existant-container" class="hidden">
                            @if($projets->isEmpty())
                            <p class="text-sm text-gray-400 italic p-2.5 border border-gray-200 rounded-lg bg-gray-50">
                                Aucun projet existant pour l'instant — créez-en un nouveau.
                            </p>
                            @else
                            <select name="projet_id"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                                <option value="">-- Choisir un projet --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->titre }}
                                </option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>

                    {{-- Thème --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thème du TDR</label>
                        <div class="relative">
                            <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required
                                class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="Ex : Recrutement des mentors 2026">
                            <button type="button" onclick="dicter('titre', this)"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700 transition"
                                title="Dicter le thème">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Supérieur hiérarchique</label>
    <select name="superieur_id" required
        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
        <option value="">-- Choisir --</option>
        @foreach($utilisateurs as $u)
        <option value="{{ $u->id }}" {{ old('superieur_id') == $u->id ? 'selected' : '' }}>
            {{ $u->nom }} {{ $u->prenom }} ({{ ucfirst($u->role) }})
        </option>
        @endforeach
    </select>
    <p class="text-xs text-red-600 mt-1 ">Cette personne sera automatiquement autorisée à approuver ce TDR, même sans permission d'approbation globale. Elle devra approuver en premier avant que les membres habilités puissent le faire.</p>
</div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <div class="relative">
                            <textarea name="description" id="description" rows="4"
                                class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm resize-none"
                                placeholder="Détails, objectifs, contexte...">{{ old('description') }}</textarea>
                            <button type="button" onclick="dicter('description', this)"
                                class="absolute right-2 top-2 text-gray-400 hover:text-green-700 transition"
                                title="Dicter la description">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fichier TDR (PDF ou Word)</label>
                        <input type="file" name="fichier" accept=".pdf,.doc,.docx"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-green-50 file:text-green-700 file:text-xs hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-1">Optionnel à cette étape  vous pourrez l'ajouter plus tard depuis la page du TDR.</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Publier le TDR
                        </button>
                        <a href="{{ route('tdr.index') }}" class="border border-gray-200 text-sm text-gray-500 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
function choisirModeProjet(mode) {
    const btnNouveau = document.getElementById('btn-nouveau-projet');
    const btnExistant = document.getElementById('btn-projet-existant');
    const containerNouveau = document.getElementById('nouveau-projet-container');
    const containerExistant = document.getElementById('projet-existant-container');

    if (mode === 'nouveau') {
        containerNouveau.classList.remove('hidden');
        containerExistant.classList.add('hidden');
        btnNouveau.classList.add('bg-green-700', 'text-white', 'border-green-700');
        btnNouveau.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
        btnExistant.classList.remove('bg-green-700', 'text-white', 'border-green-700');
        btnExistant.classList.add('bg-white', 'text-gray-600', 'border-gray-300');

        const select = containerExistant.querySelector('select');
        if (select) select.value = '';
    } else {
        containerNouveau.classList.add('hidden');
        containerExistant.classList.remove('hidden');
        btnExistant.classList.add('bg-green-700', 'text-white', 'border-green-700');
        btnExistant.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
        btnNouveau.classList.remove('bg-green-700', 'text-white', 'border-green-700');
        btnNouveau.classList.add('bg-white', 'text-gray-600', 'border-gray-300');

        containerNouveau.querySelector('input').value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    choisirModeProjet('nouveau');
});

// ---- Dictée vocale, identique au formulaire de création d'activité ----
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
</script>
</x-app-layout>