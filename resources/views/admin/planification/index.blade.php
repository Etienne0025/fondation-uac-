<x-app-layout>
<div class="flex h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">{{ $titre }}</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">

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
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString()]) }}"
                    class="text-xs font-medium px-3 py-1.5 rounded-full transition {{ !$roleFiltre ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Tous
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'staff']) }}"
                    class="text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'staff' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Staff
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'coach']) }}"
                    class="text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'coach' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Coachs
                </a>
                <a href="{{ route('admin.planification', ['semaine' => $lundi->toDateString(), 'role' => 'stagiaire']) }}"
                    class="text-xs font-medium px-3 py-1.5 rounded-full transition {{ $roleFiltre === 'stagiaire' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Stagiaires
                </a>
            </div>
            @endif

            {{-- Navigation entre semaines --}}
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('admin.planification', ['semaine' => $lundi->copy()->subWeek()->toDateString(), 'role' => $roleFiltre]) }}"
                    class="flex items-center gap-1 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Semaine précédente
                </a>

                <div class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Semaine du {{ $lundi->isoFormat('D MMMM') }} au {{ $lundi->copy()->addDays(4)->isoFormat('D MMMM YYYY') }}
                    @if(!$lundi->isSameWeek(now()))
                    <a href="{{ route('admin.planification', ['role' => $roleFiltre]) }}" class="text-xs text-green-700 hover:underline font-normal">
                        (revenir à la semaine en cours)
                    </a>
                    @endif
                </div>

                <a href="{{ route('admin.planification', ['semaine' => $lundi->copy()->addWeek()->toDateString(), 'role' => $roleFiltre]) }}"
                    class="flex items-center gap-1 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition">
                    Semaine suivante
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>

            {{-- Tableau de planification --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto shadow-sm">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-r border-gray-200 min-w-[180px] sticky left-0 bg-gray-50 z-10">
                                Nom &amp; Prénom / Membre
                            </th>
                            @foreach($jours as $jour)
                            <th class="text-center px-2 py-3 font-semibold text-gray-700 border-r border-gray-200 min-w-[150px]">
                                {{ ucfirst($jour->isoFormat('dddd')) }}
                                <div class="text-xs font-normal text-gray-400">{{ $jour->format('d/m') }}</div>
                            </th>
                            @endforeach
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 border-r border-gray-200 min-w-[220px] bg-amber-50">
                                <div class="flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Observation de l'administrateur
                                </div>
                            </th>
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 min-w-[220px] bg-blue-50">
                                <div class="flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Observation personnelle
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                            <th class="border-r border-gray-200 sticky left-0 bg-gray-50 z-10"></th>
                            @foreach($jours as $jour)
                            <th class="border-r border-gray-200 p-0">
                                <div class="grid grid-cols-2 divide-x divide-gray-200">
                                    <div class="py-1.5">Matinée<br><span class="text-gray-400">8h-12h</span></div>
                                    <div class="py-1.5">Soir<br><span class="text-gray-400">14h-17h</span></div>
                                </div>
                            </th>
                            @endforeach
                            <th class="border-r border-gray-200 bg-amber-50/50"></th>
                            <th class="bg-blue-50/50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membres as $membre)
                        @php
                            $obs = $observations[$membre->id] ?? null;
                            $peutEditerPersonnelle = $membre->id === auth()->id();
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3 border-r border-gray-200 sticky left-0 bg-white align-top">
                                <p class="font-medium text-gray-800">{{ $membre->nom }} {{ $membre->prenom }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($membre->role) }}</p>
                            </td>
                            @foreach(range(0, 4) as $i)
                            <td class="border-r border-gray-200 p-0 align-top">
                                <div class="grid grid-cols-2 divide-x divide-gray-200 h-full min-h-[64px]">
                                    <div class="p-2 space-y-1 flex flex-col justify-center">
                                        @forelse($planning[$membre->id][$i]['matin'] as $activite)
                                        <div class="bg-green-50 border border-green-200 text-green-800 text-xs rounded px-1.5 py-1">
                                            <p class="font-medium leading-tight">{{ $activite->titre }}</p>
                                            <p class="text-green-600">{{ \Carbon\Carbon::parse($activite->date)->format('H:i') }}</p>
                                        </div>
                                        @empty
                                        <span class="text-xs text-gray-300 text-center block">—</span>
                                        @endforelse
                                    </div>
                                    <div class="p-2 space-y-1 flex flex-col justify-center">
                                        @forelse($planning[$membre->id][$i]['soir'] as $activite)
                                        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded px-1.5 py-1">
                                            <p class="font-medium leading-tight">{{ $activite->titre }}</p>
                                            <p class="text-amber-600">{{ \Carbon\Carbon::parse($activite->date)->format('H:i') }}</p>
                                        </div>
                                        @empty
                                        <span class="text-xs text-gray-300 text-center block">—</span>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                            @endforeach

                            {{-- Observation de l'administrateur --}}
                            <td class="border-r border-gray-200 p-3 align-top bg-amber-50/30">
                                @if($peutEditerAdmin)
                                <form method="POST" action="{{ route('planification.observation') }}" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $membre->id }}">
                                    <input type="hidden" name="semaine_debut" value="{{ $lundi->toDateString() }}">
                                    <input type="hidden" name="champ" value="admin">
                                    <textarea name="valeur" rows="3"
                                        class="w-full text-xs p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none resize-none"
                                        placeholder="Ajouter une observation...">{{ $obs->observation_admin ?? '' }}</textarea>
                                    <button type="submit"
                                        class="w-full bg-amber-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-amber-700 transition">
                                        Enregistrer
                                    </button>
                                </form>
                                @else
                                    @if($obs->observation_admin ?? null)
                                    <div class="bg-white border border-amber-200 rounded-lg p-2.5 text-xs text-gray-700 whitespace-pre-wrap">
                                        {{ $obs->observation_admin }}
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-300 italic text-center py-3">Aucune observation</p>
                                    @endif
                                @endif
                            </td>

                            {{-- Observation personnelle --}}
                            <td class="p-3 align-top bg-blue-50/30">
                                @if($peutEditerPersonnelle)
                                <form method="POST" action="{{ route('planification.observation') }}" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $membre->id }}">
                                    <input type="hidden" name="semaine_debut" value="{{ $lundi->toDateString() }}">
                                    <input type="hidden" name="champ" value="personnelle">
                                    <textarea name="valeur" rows="3"
                                        class="w-full text-xs p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"
                                        placeholder="Votre observation...">{{ $obs->observation_personnelle ?? '' }}</textarea>
                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                                        Enregistrer
                                    </button>
                                </form>
                                @else
                                    @if($obs->observation_personnelle ?? null)
                                    <div class="bg-white border border-blue-200 rounded-lg p-2.5 text-xs text-gray-700 whitespace-pre-wrap">
                                        {{ $obs->observation_personnelle }}
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-300 italic text-center py-3">Aucune observation</p>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                Aucun membre à afficher.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>
</x-app-layout>