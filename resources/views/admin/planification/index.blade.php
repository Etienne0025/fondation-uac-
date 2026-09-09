<x-app-layout>
<div class="flex h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-auto sm:h-14 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
            <h1 class="text-base sm:text-lg font-semibold text-gray-800">{{ $titre }}</h1>
            <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full whitespace-nowrap">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-3 sm:p-6">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if($accesComplet)
            {{-- Filtre par categorie --}}
            <div class="flex items-center gap-2 mb-3 overflow-x-auto pb-1 -mx-3 px-3 sm:mx-0 sm:px-0">
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString()]) }}"
                    class="flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition {{ !$roleFiltre ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Tous
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'staff']) }}"
                    class="flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'staff' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Staff
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'coach']) }}"
                    class="flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'coach' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Coachs
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'stagiaire']) }}"
                    class="flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'stagiaire' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Stagiaires
                </a>
            </div>
            @endif

            {{-- Recherche par nom + filtre de disponibilite --}}
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
                <div class="relative flex-1 sm:max-w-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" id="rechercheMembre"
                        placeholder="Rechercher un membre..."
                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                </div>
                <div class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-lg p-1">
                    <button type="button" data-dispo="tous"
                        class="filtre-dispo-btn text-xs font-medium px-3 py-1.5 rounded-md transition bg-green-700 text-white">
                        Tous
                    </button>
                    <button type="button" data-dispo="disponible"
                        class="filtre-dispo-btn text-xs font-medium px-3 py-1.5 rounded-md transition text-gray-500 hover:bg-gray-50">
                        Disponible
                    </button>
                    <button type="button" data-dispo="occupe"
                        class="filtre-dispo-btn text-xs font-medium px-3 py-1.5 rounded-md transition text-gray-500 hover:bg-gray-50">
                        Occupé
                    </button>
                </div>
            </div>

            {{-- Navigation entre semaines --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 mb-4">
                <a href="{{ route('admin.planification', ['semaine' => $lundi->copy()->subWeek()->toDateString(), 'role' => $roleFiltre]) }}"
                    class="flex items-center justify-center sm:justify-start gap-1 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition order-1 sm:order-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span class="sm:hidden">Précédente</span>
                    <span class="hidden sm:inline">Semaine précédente</span>
                </a>

                <div class="text-xs sm:text-sm font-semibold text-gray-800 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 text-center order-first sm:order-none">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-700 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>Semaine du {{ $lundi->isoFormat('D MMMM') }} au {{ $lundi->copy()->addDays(6)->isoFormat('D MMMM YYYY') }}</span>
                    </span>
                    @if(!$lundi->isSameWeek(now()))
                    <a href="{{ route('admin.planification', ['role' => $roleFiltre]) }}" class="text-xs text-green-700 hover:underline font-normal">
                        (revenir à la semaine en cours)
                    </a>
                    @endif
                </div>

                <a href="{{ route('admin.planification', ['semaine' => $lundi->copy()->addWeek()->toDateString(), 'role' => $roleFiltre]) }}"
                    class="flex items-center justify-center sm:justify-start gap-1 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition order-2 sm:order-none">
                    <span class="sm:hidden">Suivante</span>
                    <span class="hidden sm:inline">Semaine suivante</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>

            {{-- Astuce de defilement, visible seulement sur mobile --}}
            <p class="sm:hidden text-xs text-gray-400 mb-2 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                Faites défiler horizontalement pour voir tous les jours — touchez une case pour l'agrandir
            </p>

            {{-- Tableau de planification --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto shadow-sm">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-3 sm:px-4 py-3 font-semibold text-gray-700 border-r border-gray-200 min-w-[130px] sm:min-w-[170px] sticky left-0 bg-gray-50 z-10 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.08)]">
                                Nom &amp; Prénom / Membre
                            </th>
                            @foreach($jours as $jour)
                            <th class="text-center px-2 py-3 font-semibold border-r border-gray-200 min-w-[110px] sm:min-w-[130px] {{ $jour->isSameDay(now()) ? 'bg-green-100 text-green-800' : 'text-gray-700' }}">
                                {{ ucfirst($jour->isoFormat('dddd')) }}
                                <div class="text-xs font-normal {{ $jour->isSameDay(now()) ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $jour->format('d/m') }}
                                    @if($jour->isSameDay(now()))
                                    <span class="ml-1">•&nbsp;Aujourd'hui</span>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 min-w-[240px] sm:min-w-[280px] bg-blue-50">
                                <div class="flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                    Discussion
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                            <th class="border-r border-gray-200 sticky left-0 bg-gray-50 z-10 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.08)]"></th>
                            @foreach($jours as $jour)
                            <th class="border-r border-gray-200 p-0 {{ $jour->isSameDay(now()) ? 'bg-green-50/40' : '' }}">
                                <div class="grid grid-cols-2 divide-x divide-gray-200">
                                    <div class="py-1.5">Matin<br><span class="text-gray-400">8-12h</span></div>
                                    <div class="py-1.5">Soir<br><span class="text-gray-400">14-17h</span></div>
                                </div>
                            </th>
                            @endforeach
                            <th class="bg-blue-50/50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membres as $membre)
                        @php
                            $fil = $discussions[$membre->id] ?? collect();
                            $peutParticiper = $accesComplet || $membre->id === auth()->id();

                            // Un membre est "occupe" s'il a au moins une activite cette semaine, tous jours/creneaux confondus
                            $aDesActivites = false;
                            foreach (range(0, 6) as $j) {
                                if (!empty($planning[$membre->id][$j]['matin']) || !empty($planning[$membre->id][$j]['soir'])) {
                                    $aDesActivites = true;
                                    break;
                                }
                            }
                        @endphp
                        <tr class="ligne-membre border-b border-gray-100 hover:bg-gray-50/60 transition"
                            data-nom="{{ strtolower($membre->nom . ' ' . $membre->prenom) }}"
                            data-dispo="{{ $aDesActivites ? 'occupe' : 'disponible' }}">
                            <td class="px-3 sm:px-4 py-3 border-r border-gray-200 sticky left-0 bg-white align-top shadow-[2px_0_4px_-2px_rgba(0,0,0,0.08)]">
                                <p class="font-medium text-gray-800 text-xs sm:text-sm">{{ $membre->nom }} {{ $membre->prenom }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($membre->role) }}</p>
                            </td>
                            @foreach(range(0, 6) as $i)
                            <td class="border-r border-gray-200 p-0 align-top {{ $i === (int) now()->dayOfWeekIso - 1 && $lundi->isSameWeek(now()) ? 'bg-green-50/40' : '' }}">
                                <div class="grid grid-cols-2 divide-x divide-gray-200 h-[180px] sm:h-[222px]">
                                    <div class="cellule-zoomable p-2 space-y-1 overflow-y-auto cursor-zoom-in hover:bg-gray-50/80 transition"
                                        data-titre="{{ $membre->nom }} {{ $membre->prenom }} — {{ ucfirst($jours[$i]->isoFormat('dddd')) }} {{ $jours[$i]->format('d/m') }} (Matin, 8-12h)">
                                        @forelse($planning[$membre->id][$i]['matin'] as $activite)
                                        <div class="bg-green-50 border border-green-200 text-green-800 text-xs rounded px-1.5 py-1">
                                            <p class="font-medium leading-tight">{{ $activite->titre }}</p>
                                            <p class="text-green-600">{{ \Carbon\Carbon::parse($activite->date)->format('H:i') }}</p>
                                        </div>
                                        @empty
                                        <span class="text-xs text-gray-300 text-center block mt-2">—</span>
                                        @endforelse
                                    </div>
                                    <div class="cellule-zoomable p-2 space-y-1 overflow-y-auto cursor-zoom-in hover:bg-gray-50/80 transition"
                                        data-titre="{{ $membre->nom }} {{ $membre->prenom }} — {{ ucfirst($jours[$i]->isoFormat('dddd')) }} {{ $jours[$i]->format('d/m') }} (Soir, 14-17h)">
                                        @forelse($planning[$membre->id][$i]['soir'] as $activite)
                                        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded px-1.5 py-1">
                                            <p class="font-medium leading-tight">{{ $activite->titre }}</p>
                                            <p class="text-amber-600">{{ \Carbon\Carbon::parse($activite->date)->format('H:i') }}</p>
                                        </div>
                                        @empty
                                        <span class="text-xs text-gray-300 text-center block mt-2">—</span>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                            @endforeach

                            {{-- Discussion (fil de commentaires) --}}
                            <td class="p-2 sm:p-3 align-top bg-blue-50/20">
                                <div class="cellule-zoomable-discussion cursor-zoom-in"
                                    data-titre="Discussion — {{ $membre->nom }} {{ $membre->prenom }}"
                                    data-membre-id="{{ $membre->id }}"
                                    data-peut-participer="{{ $peutParticiper ? '1' : '0' }}">
                                    <div class="flex flex-col gap-2 h-[160px] sm:h-[190px] overflow-y-auto mb-2 pr-1 pointer-events-none">
                                        @forelse($fil as $msg)
                                        @php $estMoi = $msg->user_id === auth()->id(); @endphp
                                        <div class="flex flex-col {{ $estMoi ? 'items-end' : 'items-start' }} flex-shrink-0">
                                            <div class="max-w-[85%] {{ $estMoi ? 'bg-green-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }} text-xs rounded-lg px-2.5 py-1.5">
                                                @if(!$estMoi)
                                                <p class="font-semibold text-[11px] {{ $msg->auteur->role === 'administrateur' ? 'text-amber-600' : 'text-blue-600' }}">
                                                    {{ $msg->auteur->nom }} {{ $msg->auteur->prenom }}
                                                </p>
                                                @endif
                                                <p class="whitespace-pre-wrap">{{ $msg->message }}</p>
                                            </div>
                                            <span class="text-[10px] text-gray-400 mt-0.5">{{ $msg->created_at->isoFormat('D MMM, H:mm') }}</span>
                                        </div>
                                        @empty
                                        <p class="text-xs text-gray-300 italic text-center py-4">Aucun message pour l'instant</p>
                                        @endforelse
                                    </div>
                                    <p class="text-[10px] text-gray-400 text-center pointer-events-none">Toucher pour agrandir</p>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                Aucun membre à afficher.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p id="aucunResultat" class="hidden text-center text-sm text-gray-400 py-8">
                Aucun membre ne correspond à votre recherche.
            </p>

        </main>
    </div>
</div>

{{-- Modale de zoom, partagee entre les cases jour/creneau et la discussion --}}
<div id="zoomModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 id="zoomTitre" class="text-sm font-semibold text-gray-800"></h3>
            <button type="button" id="zoomFermer" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="zoomContenu" class="flex-1 overflow-y-auto p-5"></div>
        <div id="zoomFormulaireWrapper" class="hidden border-t border-gray-200 p-4"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- Recherche par nom ----
    const inputRecherche = document.getElementById('rechercheMembre');
    const lignes = document.querySelectorAll('.ligne-membre');
    const aucunResultat = document.getElementById('aucunResultat');
    let filtreDispoActif = 'tous';

    function appliquerFiltres() {
        const terme = inputRecherche.value.trim().toLowerCase();
        let visibles = 0;

        lignes.forEach(ligne => {
            const correspondNom = ligne.dataset.nom.includes(terme);
            const correspondDispo = filtreDispoActif === 'tous' || ligne.dataset.dispo === filtreDispoActif;
            const visible = correspondNom && correspondDispo;
            ligne.style.display = visible ? '' : 'none';
            if (visible) visibles++;
        });

        aucunResultat.classList.toggle('hidden', visibles > 0);
    }

    inputRecherche.addEventListener('input', appliquerFiltres);

    // ---- Filtre de disponibilite ----
    document.querySelectorAll('.filtre-dispo-btn').forEach(bouton => {
        bouton.addEventListener('click', () => {
            filtreDispoActif = bouton.dataset.dispo;

            document.querySelectorAll('.filtre-dispo-btn').forEach(b => {
                b.classList.remove('bg-green-700', 'text-white');
                b.classList.add('text-gray-500');
            });
            bouton.classList.add('bg-green-700', 'text-white');
            bouton.classList.remove('text-gray-500');

            appliquerFiltres();
        });
    });

    // ---- Zoom sur une case matin/soir ----
    const modal = document.getElementById('zoomModal');
    const zoomTitre = document.getElementById('zoomTitre');
    const zoomContenu = document.getElementById('zoomContenu');
    const zoomFormulaireWrapper = document.getElementById('zoomFormulaireWrapper');

    function ouvrirModal() {
        modal.classList.remove('hidden');
    }

    function fermerModal() {
        modal.classList.add('hidden');
        zoomFormulaireWrapper.classList.add('hidden');
        zoomFormulaireWrapper.innerHTML = '';
    }

    document.getElementById('zoomFermer').addEventListener('click', fermerModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) fermerModal();
    });

    document.querySelectorAll('.cellule-zoomable').forEach(cellule => {
        cellule.addEventListener('click', () => {
            zoomTitre.textContent = cellule.dataset.titre;
            zoomContenu.innerHTML = cellule.innerHTML.replace(/text-xs/g, 'text-sm').replace(/px-1\.5 py-1/g, 'px-3 py-2');
            zoomFormulaireWrapper.classList.add('hidden');
            ouvrirModal();
        });
    });

    // ---- Zoom sur la discussion (avec formulaire d'envoi fonctionnel) ----
    document.querySelectorAll('.cellule-zoomable-discussion').forEach(cellule => {
        cellule.addEventListener('click', () => {
            zoomTitre.textContent = cellule.dataset.titre;

            const fil = cellule.querySelector('.flex.flex-col.gap-2');
            zoomContenu.innerHTML = fil ? fil.outerHTML.replace('h-[160px] sm:h-[190px]', 'h-[50vh]').replace('pointer-events-none', '') : '';

            const peutParticiper = cellule.dataset.peutParticiper === '1';
            if (peutParticiper) {
                const membreId = cellule.dataset.membreId;
                zoomFormulaireWrapper.innerHTML = `
                    <form method="POST" action="{{ route('planification.discussion') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="membre_id" value="${membreId}">
                        <input type="hidden" name="semaine_debut" value="{{ $lundi->toDateString() }}">
                        <input type="text" name="message" required maxlength="2000"
                            class="flex-1 text-sm p-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            placeholder="Écrire un message...">
                        <button type="submit"
                            class="bg-blue-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-blue-700 transition flex-shrink-0">
                            Envoyer
                        </button>
                    </form>
                `;
                zoomFormulaireWrapper.classList.remove('hidden');
            } else {
                zoomFormulaireWrapper.classList.add('hidden');
            }

            ouvrirModal();
        });
    });
});
</script>
</x-app-layout>