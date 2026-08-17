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
                    Semaine du {{ $lundi->isoFormat('D MMMM') }} au {{ $lundi->copy()->addDays(6)->isoFormat('D MMMM YYYY') }}
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
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-r border-gray-200 min-w-[170px] sticky left-0 bg-gray-50 z-10">
                                Nom &amp; Prénom / Membre
                            </th>
                            @foreach($jours as $jour)
                            <th class="text-center px-2 py-3 font-semibold border-r border-gray-200 min-w-[130px] {{ $jour->isSameDay(now()) ? 'bg-green-100 text-green-800' : 'text-gray-700' }}">
                                {{ ucfirst($jour->isoFormat('dddd')) }}
                                <div class="text-xs font-normal {{ $jour->isSameDay(now()) ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $jour->format('d/m') }}
                                    @if($jour->isSameDay(now()))
                                    <span class="ml-1">•&nbsp;Aujourd'hui</span>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                            <th class="text-center px-3 py-3 font-semibold text-gray-700 min-w-[280px] bg-blue-50">
                                <div class="flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                    Discussion
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                            <th class="border-r border-gray-200 sticky left-0 bg-gray-50 z-10"></th>
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
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3 border-r border-gray-200 sticky left-0 bg-white align-top">
                                <p class="font-medium text-gray-800">{{ $membre->nom }} {{ $membre->prenom }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($membre->role) }}</p>
                            </td>
                            @foreach(range(0, 6) as $i)
                            <td class="border-r border-gray-200 p-0 align-top {{ $i === (int) now()->dayOfWeekIso - 1 && $lundi->isSameWeek(now()) ? 'bg-green-50/40' : '' }}">
                                <div class="grid grid-cols-2 divide-x divide-gray-200 h-[222px]">
                                    <div class="p-2 space-y-1 overflow-y-auto">
                                        @forelse($planning[$membre->id][$i]['matin'] as $activite)
                                        <div class="bg-green-50 border border-green-200 text-green-800 text-xs rounded px-1.5 py-1">
                                            <p class="font-medium leading-tight">{{ $activite->titre }}</p>
                                            <p class="text-green-600">{{ \Carbon\Carbon::parse($activite->date)->format('H:i') }}</p>
                                        </div>
                                        @empty
                                        <span class="text-xs text-gray-300 text-center block mt-2">—</span>
                                        @endforelse
                                    </div>
                                    <div class="p-2 space-y-1 overflow-y-auto">
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
                            <td class="p-3 align-top bg-blue-50/20">
                                <div class="flex flex-col gap-2 h-[190px] overflow-y-auto mb-2 pr-1">
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

                                @if($peutParticiper)
                                <form method="POST" action="{{ route('planification.discussion') }}" class="flex gap-1.5">
                                    @csrf
                                    <input type="hidden" name="membre_id" value="{{ $membre->id }}">
                                    <input type="hidden" name="semaine_debut" value="{{ $lundi->toDateString() }}">
                                    <input type="text" name="message" required maxlength="2000"
                                        class="flex-1 text-xs p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                        placeholder="Écrire un message...">
                                    <button type="submit"
                                        class="bg-blue-600 text-white text-xs font-medium px-3 py-2 rounded-lg hover:bg-blue-700 transition flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
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

        </main>
    </div>
</div>
</x-app-layout>