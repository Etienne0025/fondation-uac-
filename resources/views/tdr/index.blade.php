<x-app-layout>
<div class="flex min-h-screen md:h-screen bg-gray-100">

    @if(auth()->user()->role === 'administrateur')
        <x-sidebar-admin />
    @else
        <x-sidebar-responsable />
    @endif

    <div class="flex-1 min-w-0 flex flex-col md:overflow-hidden">
        <header class="min-h-14 bg-white border-b border-gray-200 px-4 md:px-6 py-2 flex items-center justify-between gap-3">
            <h1 class="text-base md:text-lg font-semibold text-gray-800 truncate">Termes de référence</h1>
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                <span class="hidden lg:block text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full truncate max-w-[10rem] sm:max-w-none">
                    {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
                </div>
            </div>
        </header>

        <main class="flex-1 md:overflow-y-auto p-4 md:p-6">


            {{-- Filtres --}}
            <form method="GET" action="{{ route('tdr.index') }}"
                  class="mb-4 bg-white border border-gray-200 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Thème</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Mot-clé..."
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Publié par</label>
                    <select name="user_id"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                        <option value="">Tous</option>
                        @foreach($publieurs as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nom }} {{ $u->prenom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm">
                </div>

                <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit"
                        class="flex-1 lg:flex-none bg-green-700 text-white text-xs font-medium px-4 py-2.5 rounded-lg hover:bg-green-800 transition">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['q','user_id','date_debut','date_fin']))
                    <a href="{{ route('tdr.index') }}"
                        class="flex-1 lg:flex-none text-center border border-gray-200 text-xs text-gray-500 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition">
                        Réinitialiser
                    </a>
                    @endif
                </div>
            </form>

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                <h2 class="text-sm font-semibold text-gray-800">Liste des TDR publiés</h2>
                @if(in_array(auth()->user()->role, ['administrateur', 'responsable']))
                <a href="{{ route('tdr.create') }}"
                    class="flex items-center justify-center gap-1.5 bg-green-700 text-white text-xs font-medium px-4 py-2.5 sm:py-2 rounded-lg hover:bg-green-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Publier un TDR
                </a>
                @endif
            </div>

            {{-- ========== VUE MOBILE : cartes (< md) ========== --}}
            <div class="md:hidden space-y-3">
                @forelse($tdrs as $tdr)
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 break-words">{{ $tdr->titre }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 break-words">{{ $tdr->projet->titre }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-medium px-2.5 py-1 rounded-full {{ $tdr->nombreApprobations() > 0 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $tdr->nombreApprobations() }} approbation{{ $tdr->nombreApprobations() > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                        <div>
                            <dt class="text-gray-400">Publié par</dt>
                            <dd class="text-gray-700 break-words">{{ $tdr->user->nom }} {{ $tdr->user->prenom }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Date</dt>
                            <dd class="text-gray-700">{{ $tdr->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Fichier</dt>
                            <dd>
                                @if($tdr->fichier_chemin)
                                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ strtoupper($tdr->fichier_type) }}</span>
                                @else
                                <span class="text-gray-300">Aucun</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400">Avis</dt>
                            <dd class="text-gray-700">{{ $tdr->avis_count ?? $tdr->avis->count() }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('tdr.show', $tdr) }}"
                            class="flex-1 text-center text-xs border border-gray-200 rounded-md px-3 py-2 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition">
                            Voir
                        </a>
                        @if($tdr->user_id === auth()->id() && $tdr->nombreApprobations() === 0)
                        <a href="{{ route('tdr.edit', $tdr) }}"
                            class="flex-1 text-center text-xs border border-gray-200 rounded-md px-3 py-2 text-gray-500 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 transition">
                            Modifier
                        </a>
                        <form action="{{ route('tdr.destroy', $tdr) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Supprimer ce TDR ? Cette action est irréversible.')"
                                class="w-full text-xs border border-gray-200 rounded-md px-3 py-2 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                Supprimer
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-12 text-center text-sm text-gray-400">
                    Aucun TDR ne correspond à votre recherche.
                </div>
                @endforelse
            </div>

            {{-- ========== VUE TABLETTE / BUREAU : tableau (>= md) ========== --}}
            <div class="hidden md:block bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[900px]">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Projet</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Thème</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Publié par</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Date</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Fichier</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Statut</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Avis</th>
                                <th class="px-4 lg:px-5 py-3 text-xs font-medium text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tdrs as $tdr)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 lg:px-5 py-3 text-sm text-gray-600">{{ $tdr->projet->titre }}</td>
                                <td class="px-4 lg:px-5 py-3 text-sm font-medium text-gray-800">{{ $tdr->titre }}</td>
                                <td class="px-4 lg:px-5 py-3 text-sm text-gray-500">{{ $tdr->user->nom }} {{ $tdr->user->prenom }}</td>
                                <td class="px-4 lg:px-5 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $tdr->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 lg:px-5 py-3 text-sm">
                                    @if($tdr->fichier_chemin)
                                    <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ strtoupper($tdr->fichier_type) }}</span>
                                    @else
                                    <span class="text-xs text-gray-300">Aucun</span>
                                    @endif
                                </td>
                                <td class="px-4 lg:px-5 py-3">
                                    <span class="whitespace-nowrap text-xs font-medium px-2.5 py-1 rounded-full {{ $tdr->nombreApprobations() > 0 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $tdr->nombreApprobations() }} approbation{{ $tdr->nombreApprobations() > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="px-4 lg:px-5 py-3 text-sm text-gray-500">{{ $tdr->avis_count ?? $tdr->avis->count() }}</td>
                                <td class="px-4 lg:px-5 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('tdr.show', $tdr) }}"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition">
                                            Voir
                                        </a>
                                        @if($tdr->user_id === auth()->id() && $tdr->nombreApprobations() === 0)
                                        <a href="{{ route('tdr.edit', $tdr) }}"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 transition">
                                            Modifier
                                        </a>
                                        <form action="{{ route('tdr.destroy', $tdr) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Supprimer ce TDR ? Cette action est irréversible.')"
                                                class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                                Supprimer
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                                    Aucun TDR ne correspond à votre recherche.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination (commune aux deux vues) --}}
            <div class="mt-4">{{ $tdrs->appends(request()->query())->links() }}</div>

        </main>
    </div>
</div>
</x-app-layout>