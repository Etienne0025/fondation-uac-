<x-app-layout>
    <!-- 1. Image d'arrière-plan forcée en fond d'écran via fixed/z-0 -->
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('image/gb.jpg') }}');"></div>

    <!-- 2. Voile sombre très léger pour la lisibilité -->
    <div class="fixed inset-0 z-0 bg-black/10 pointer-events-none"></div>

    <!-- 3. Contenu applicatif placé par-dessus avec z-10 -->
    <div class="relative z-10 flex h-screen">
        @if(auth()->user()->role === 'administrateur')
            <x-sidebar-admin />
        @else
            <x-sidebar-responsable />
        @endif

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-auto sm:h-14 bg-white/90 backdrop-blur-md border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                <h1 class="text-base sm:text-lg font-semibold text-gray-800">Modifier le TDR</h1>
                <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                    <span class="text-xs text-gray-500 font-medium">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                    <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full whitespace-nowrap shadow-sm">
                        {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-3 sm:p-6">

                @if($errors->any())
                <div class="mb-4 bg-red-50/90 backdrop-blur-sm border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="max-w-2xl bg-white/95 backdrop-blur-md border border-gray-200/80 rounded-xl p-4 sm:p-6 shadow-xl">

                    <p class="text-xs text-gray-500 mb-4 font-medium">Projet : {{ $tdr->projet->titre }} (non modifiable)</p>

                    <form method="POST" action="{{ route('tdr.update', $tdr) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thème du TDR</label>
                            <div class="relative">
                                <input type="text" name="titre" id="titre" value="{{ old('titre', $tdr->titre) }}" required
                                    class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white/80 focus:bg-white">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <div class="relative">
                                <textarea name="description" id="description" rows="4"
                                    class="w-full p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm resize-none bg-white/80 focus:bg-white">{{ old('description', $tdr->description) }}</textarea>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supérieur hiérarchique</label>
                            <select name="superieur_id" required
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white/80 focus:bg-white">
                                <option value="">-- Choisir --</option>
                                @foreach($utilisateurs as $u)
                                <option value="{{ $u->id }}" {{ old('superieur_id', $tdr->superieur_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->nom }} {{ $u->prenom }} ({{ ucfirst($u->role) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition shadow-sm">
                                Enregistrer les modifications
                            </button>
                            <a href="{{ route('tdr.show', $tdr) }}" class="border border-gray-200 text-sm text-gray-600 px-6 py-2.5 rounded-lg hover:bg-white/80 transition bg-white/50">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>

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