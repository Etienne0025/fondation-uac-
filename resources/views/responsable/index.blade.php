<x-app-layout>
<div class="flex h-screen bg-gray-100">

    <x-sidebar-responsable />

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Mes activités</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">

            @php
                $prefixe = auth()->user()->role === 'Administrateur' ? 'admin.activites' : 'responsable';
            @endphp

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-400">Total activités</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['en_attente'] }}</p>
                        <p class="text-xs text-gray-400">En attente</p>
                    </div>
                </div>
            </div>

            {{-- Recherche et filtres --}}
            <form method="GET"
                action="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.index') : route('responsable.index') }}"
                class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
                <div class="grid grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Rechercher</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Titre de l'activité..."
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                        <select name="statut"
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-green-600 focus:border-green-600 outline-none bg-white">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="passee"     {{ request('statut') == 'passee'     ? 'selected' : '' }}>Passée</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="submit"
                        class="bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'date_debut', 'date_fin', 'statut']))
                    <a href="{{ auth()->user()->role === 'administrateur' ? route('admin.activites.index') : route('responsable.index') }}"
                        class="border border-gray-200 text-xs text-gray-500 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                        Réinitialiser
                    </a>
                    @endif
                </div>

                @if(request()->hasAny(['search', 'date_debut', 'date_fin', 'statut']))
                <div class="mt-3 flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-400">Filtres actifs :</span>
                    @if(request('search'))
                        <span class="text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full">
                            Recherche : "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('date_debut'))
                        <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full">
                            Depuis : {{ \Carbon\Carbon::parse(request('date_debut'))->isoFormat('D MMM YYYY') }}
                        </span>
                    @endif
                    @if(request('date_fin'))
                        <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full">
                            Jusqu'au : {{ \Carbon\Carbon::parse(request('date_fin'))->isoFormat('D MMM YYYY') }}
                        </span>
                    @endif
                    @if(request('statut'))
                        <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">
                            Statut : {{ str_replace('_', ' ', ucfirst(request('statut'))) }}
                        </span>
                    @endif
                    <span class="text-xs text-gray-400">— {{ $activites->total() }} résultat(s)</span>
                </div>
                @endif
            </form>

            {{-- Tableau --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-semibold text-gray-800">Liste de mes activités</h2>
                    <a href="{{ route($prefixe . '.create') }}"
                        class="flex items-center gap-1.5 bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouvelle activité
                    </a>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-xs font-medium text-gray-400">Titre</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Date et heure</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Lieu</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Accès</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Statut</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activites as $activite)
                        @php
                            $statutColors = [
                                'en_attente' => 'bg-amber-100 text-amber-700',
                                'passee'     => 'bg-gray-100 text-gray-600',
                            ];
                            $accesColors = [
                                'public'    => 'bg-blue-100 text-blue-700',
                                'prive'     => 'bg-gray-100 text-gray-600',
                                'restreint' => 'bg-purple-100 text-purple-700',
                            ];
                            $droitacces    = $activite->acces->droitacces ?? 'public';
                            $mEstProprietaire = $activite->user_id === auth()->id();
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-800 font-medium">
                                {{ $activite->titre }}
                                @if(!$mEstProprietaire)
                                <span class="ml-1 text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">
                                    {{ $activite->user->nom ?? '' }}
                                </span>
                                @endif
                            </td>
                            <td class="py-3 text-sm text-gray-500">
                                <div>{{ $activite->date->isoFormat('D MMM YYYY') }}</div>
                                <div class="text-xs text-gray-400">{{ $activite->date->format('H\hi') }}</div>
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $activite->lieu }}</td>
                            <td class="py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $accesColors[$droitacces] }}">
                                    {{ ucfirst($droitacces) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statutColors[$activite->statut] }}">
                                    {{ str_replace('_', ' ', ucfirst($activite->statut)) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2 flex-wrap">
                                    @if($mEstProprietaire)
                                        {{-- Bouton Modifier : seulement si l'activité n'est pas encore passée --}}
                                        @if($activite->statut !== 'passee')
                                        <a href="{{ route($prefixe . '.edit', $activite) }}"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500
                                                   hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition">
                                            Modifier
                                        </a>
                                        @endif

                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route($prefixe . '.destroy', $activite) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Supprimer cette activité ?')"
                                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500
                                                       hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                                Supprimer
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Lecture seule</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-400">
                                @if(request()->hasAny(['search', 'date_debut', 'date_fin', 'statut']))
                                    Aucune activité ne correspond à vos critères.
                                @else
                                    Aucune activité pour le moment.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $activites->links() }}
                </div>
            </div>

        </main>
    </div>
</div>
</x-app-layout>