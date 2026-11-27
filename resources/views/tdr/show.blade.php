<x-app-layout>
<div class="flex h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-auto sm:h-14 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
            <h1 class="text-base sm:text-lg font-semibold text-gray-800">{{ $tdr->titre }}</h1>
            <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full whitespace-nowrap">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-3 sm:p-6">



            <a href="{{ route('tdr.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Retour à la liste
            </a>

            {{-- Empile en 1 colonne sur mobile, revient à 3 colonnes à partir de lg --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Colonne principale --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Infos du TDR --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <span class="text-xs text-gray-400">Projet : {{ $tdr->projet->titre }}</span>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full self-start sm:self-auto {{ $tdr->nombreApprobations() > 0 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $tdr->nombreApprobations() }} approbation{{ $tdr->nombreApprobations() > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">{{ $tdr->titre }}</h2>
                        @if($tdr->description)
                        <p class="text-sm text-gray-600 whitespace-pre-wrap mb-3">{{ $tdr->description }}</p>
                        @endif
                        <p class="text-xs text-gray-400">
                            Publié par {{ $tdr->user->nom }} {{ $tdr->user->prenom }} le {{ $tdr->created_at->isoFormat('D MMMM YYYY') }}
                        </p>
                        @if($tdr->superieur)
                        <p class="text-xs text-gray-400 mt-1">
                            Supérieur hiérarchique désigné : {{ $tdr->superieur->nom }} {{ $tdr->superieur->prenom }}
                        </p>
                        @endif

                        @if($tdr->approbations->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-500 mb-1.5">Approuvé par :</p>
                            <div class="space-y-1">
                                @foreach($tdr->approbations as $approbation)
                                <p class="text-xs text-green-600">
                                    ✓ {{ $approbation->user->nom }} {{ $approbation->user->prenom }} — {{ $approbation->created_at->isoFormat('D MMM YYYY [à] H[h]mm') }}
                                </p>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                  {{-- Fichier joint --}}
<div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
    <h3 class="text-sm font-semibold text-gray-800 mb-3">Fichier TDR</h3>

    @if($tdr->fichier_chemin)
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $tdr->fichier_nom }}</p>
                <p class="text-xs text-gray-400">
                    Soumis par {{ $tdr->soumisPar->nom ?? '—' }} le {{ $tdr->fichier_soumis_a?->isoFormat('D MMM YYYY') }}
                </p>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <button type="button" onclick="toggleVisionneuse()"
                class="text-xs border border-green-200 rounded-md px-3 py-1.5 text-green-700 hover:bg-green-50 transition">
                Lire le document
            </button>
            <a href="{{ Storage::url($tdr->fichier_chemin) }}" target="_blank"
                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition text-center">
                Télécharger
            </a>
        </div>
    </div>

    {{-- Visionneuse intégrée, masquée par défaut --}}
    <div id="visionneuse-tdr" class="hidden mt-3">
        @if($tdr->fichier_type === 'pdf')
        <iframe src="{{ Storage::url($tdr->fichier_chemin) }}"
            class="w-full h-[500px] sm:h-[700px] border border-gray-200 rounded-lg"
            title="Aperçu du fichier TDR">
        </iframe>
        @else
        <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-amber-700 mb-2">
                La lecture intégrée des fichiers Word nécessite que la plateforme soit accessible publiquement (elle ne fonctionne pas en environnement local).
            </p>
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(Storage::url($tdr->fichier_chemin)) }}"
                class="w-full h-[500px] sm:h-[700px] border border-gray-200 rounded-lg"
                title="Aperçu du fichier TDR">
            </iframe>
        </div>
        @endif
    </div>
    @else
    <p class="text-sm text-gray-400 italic">Aucun fichier soumis pour l'instant.</p>
    @endif

    {{-- Seul le créateur du TDR peut ajouter/remplacer le fichier --}}
    @if($tdr->user_id === auth()->id())
    <form method="POST" action="{{ route('tdr.fichier', $tdr) }}" enctype="multipart/form-data" class="mt-3 flex flex-col sm:flex-row gap-2">
        @csrf
        <input type="file" name="fichier" accept=".pdf,.doc,.docx" required
            class="flex-1 min-w-0 text-xs p-2 border border-gray-300 rounded-lg file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:bg-green-50 file:text-green-700 file:text-xs">
        <button type="submit"
            class="bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition flex-shrink-0">
            {{ $tdr->fichier_chemin ? 'Remplacer' : 'Soumettre' }}
        </button>
    </form>
    @endif
</div>

                    {{-- Avis --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Avis ({{ $tdr->avis->count() }})</h3>

                        <div class="space-y-3 mb-4">
                            @forelse($tdr->avis as $avis)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-700 text-white text-xs font-semibold flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($avis->user->nom, 0, 1)) }}{{ strtoupper(substr($avis->user->prenom, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-0.5">
                                        <p class="text-xs font-semibold text-gray-700">{{ $avis->user->nom }} {{ $avis->user->prenom }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $avis->created_at->isoFormat('D MMM, H:mm') }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap break-words">{{ $avis->message }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-400 italic text-center py-4">Aucun avis pour l'instant.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('tdr.avis', $tdr) }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <div class="relative flex-1 min-w-0">
                                <input type="text" name="message" id="message-avis" required maxlength="2000"
                                    class="w-full text-sm p-2.5 pr-11 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none"
                                    placeholder="Donner votre avis...">
                                <button type="button" onclick="dicter('message-avis', this)"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700 transition"
                                    title="Dicter l'avis">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                    </svg>
                                </button>
                            </div>
                            <button type="submit"
                                class="bg-blue-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-blue-700 transition flex-shrink-0">
                                Envoyer
                            </button>
                        </form>
                    </div>

                </div>

                {{-- Colonne latérale --}}
                <div class="space-y-4">
                    @php
                        $estSuperieur = $tdr->superieur_id === auth()->id();
                        $peutApprouver = $tdr->peutEtreApprouvePar(auth()->user());
                        $dejaApprouve = $tdr->estApprouvePar(auth()->user());
                    @endphp

                    {{-- Bloc "Approbation" dans la colonne latérale --}}
                    @if($estSuperieur || auth()->user()->peut_approuver_tdr)
                        @if($dejaApprouve)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 sm:p-5">
                            <p class="text-sm text-green-700">✓ Vous avez déjà approuvé ce TDR.</p>
                        </div>
                        @else
                        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2">Approbation</h3>
                            @if(!$peutApprouver)
                            <p class="text-xs text-gray-500 mb-3">
                                En attente de l'approbation du supérieur hiérarchique
                                (<strong>{{ $tdr->superieur->nom }} {{ $tdr->superieur->prenom }}</strong>)
                                avant que les autres membres habilités puissent approuver ce TDR.
                            </p>
                            <button type="button" disabled
                                class="w-full bg-gray-200 text-gray-400 text-sm font-medium px-4 py-2.5 rounded-lg cursor-not-allowed">
                                Approuver ce TDR
                            </button>
                            @else
                            <p class="text-xs text-gray-500 mb-3">
                                @if($estSuperieur)
                                    Vous avez été désigné(e) supérieur hiérarchique pour ce TDR, ce qui vous autorise à l'approuver.
                                @else
                                    Le supérieur hiérarchique a approuvé ce TDR, vous pouvez désormais l'approuver à votre tour.
                                @endif
                            </p>
                            <form method="POST" action="{{ route('tdr.approuver', $tdr) }}"
                                onsubmit="return confirm('Confirmer votre approbation de ce TDR ?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-green-800 transition">
                                    Approuver ce TDR
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif
                    @endif
                </div>

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

function toggleVisionneuse() {
    const visionneuse = document.getElementById('visionneuse-tdr');
    visionneuse.classList.toggle('hidden');
}
</script>
</x-app-layout>