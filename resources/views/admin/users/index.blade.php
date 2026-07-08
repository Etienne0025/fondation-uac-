<x-app-layout>
<div class="flex h-screen bg-gray-100">

    {{-- Sidebar --}}
    <aside class="w-60 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-5 border-b border-gray-200 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-800 font-semibold text-sm">F</div>
            <div>
                <p class="font-semibold text-sm text-gray-800">FUAC Agenda</p>
                <p class="text-xs text-gray-400">Fondation UAC</p>
            </div>
        </div>

        <nav class="flex-1 p-3 space-y-0.5">
            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Principal</p>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
                Dashboard
            </a>

            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Utilisateurs</p>
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm bg-green-100 text-green-800 font-medium">
                Gestion utilisateurs
            </a>

            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Agenda</p>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">Événements</a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">Activités</a>
            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">Notifications</a>
        </nav>

        <div class="p-4 border-t border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-green-700 text-white text-xs font-semibold flex items-center justify-center">
                {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->nom }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Gestion des utilisateurs</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->translatedFormat('l d F Y') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    Administrateur
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- Message flash --}}
            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-4 gap-3 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-lg">👥</div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $users->count() }}</p>
                        <p class="text-xs text-gray-400">Utilisateurs total</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">✅</div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $users->where('active', true)->count() }}</p>
                        <p class="text-xs text-gray-400">Comptes actifs</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">⛔</div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $users->where('active', false)->count() }}</p>
                        <p class="text-xs text-gray-400">Comptes inactifs</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center text-lg">🛡️</div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $users->where('role', 'admin')->count() }}</p>
                        <p class="text-xs text-gray-400">Admins</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-semibold text-gray-800">Liste des utilisateurs</h2>
                    <a href="{{ route('register') }}" class="bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                        + Ajouter un utilisateur
                    </a>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-xs font-medium text-gray-400">Nom complet</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Email</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Rôle</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Statut</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $roleColors = [
                                'admin'        => 'bg-green-100 text-green-700',
                                'responsable'  => 'bg-blue-100 text-blue-700',
                                'mentor'       => 'bg-amber-100 text-amber-700',
                                'invite'       => 'bg-purple-100 text-purple-700',
                            ];
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-800">{{ $user->nom }} {{ $user->prenom }}</td>
                            <td class="py-3 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-600">
                                <span class="inline-block w-2 h-2 rounded-full mr-1.5 {{ $user->active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $user->active ? 'Actif' : 'Inactif' }}
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    {{-- Activer / Désactiver --}}
                                    <form action="{{ route('admin.toggle', $user) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500
                                                   hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 transition">
                                            {{ $user->active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>

                                    {{-- Supprimer --}}
                                    <form action="{{ route('admin.destroy', $user) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Confirmer la suppression de {{ $user->nom }} ?')"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500
                                                   hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-gray-400">Aucun utilisateur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>
</x-app-layout>