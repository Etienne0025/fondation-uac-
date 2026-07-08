<x-app-layout>
<div class="flex h-screen bg-gray-100">

    {{-- Sidebar Admin --}}
    @php
        $nonLues = \App\Models\Notification::whereHas('activite', function($q) {
            $q->where('user_id', auth()->id());
        })->where('envoye', true)->where('vue', false)->count();
    @endphp

    <aside class="w-60 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-5 border-b border-gray-200 flex items-center gap-3">
            <img src="/image/logo-fuac.jpg" alt="Logo" class="h-10 w-auto object-contain">
            <div>
                <p class="font-semibold text-sm text-gray-800">Startup-Valley</p>
                <p class="text-xs text-gray-400">Fondation UAC</p>
            </div>
        </div>

        <nav class="flex-1 p-3 space-y-0.5">

            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Administration</p>
            <a href="{{ route('admin.index') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm bg-green-100 text-green-800 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Gestion utilisateurs
            </a>

            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Agenda</p>
            <a href="{{ route('responsable.index') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Mes activités
            </a>

            <a href="{{ route('responsable.notifications') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                Notifications
                @if($nonLues > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                    {{ $nonLues > 99 ? '99+' : $nonLues }}
                </span>
                @endif
            </a>

            <a href="{{ route('reseau.index') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253" />
                </svg>
                Réseaux sociaux
            </a>

            <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Compte</p>
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Mon profil
            </a>
        </nav>

        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-green-700 text-white text-xs font-semibold flex items-center justify-center">
                    {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ auth()->user()->nom }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-xs text-red-500 hover:text-red-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-14 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">Gestion des utilisateurs</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    Administrateur
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-4 gap-3 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-400">Utilisateurs total</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['actifs'] }}</p>
                        <p class="text-xs text-gray-400">Comptes actifs</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['inactifs'] }}</p>
                        <p class="text-xs text-gray-400">Comptes inactifs</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25 4.5-4.5m8.25 2.25a11.953 11.953 0 01-9 3.827 11.953 11.953 0 01-9-3.827V12c0-5.385 3.823-9.879 9-10.82C19.177 2.121 23 6.615 23 12v.75z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-semibold text-gray-800">{{ $stats['admins'] }}</p>
                        <p class="text-xs text-gray-400">Admins</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-semibold text-gray-800">Liste des utilisateurs</h2>
                    <a href="{{ route('admin.create') }}"
                        class="flex items-center gap-1.5 bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajouter un utilisateur
                    </a>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 text-xs font-medium text-gray-400">Nom complet</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Email</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Téléphone</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Rôle</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Statut</th>
                            <th class="pb-3 text-xs font-medium text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $roleColors = [
                                'administrateur' => 'bg-green-100 text-green-700',
                                'responsable'    => 'bg-blue-100 text-blue-700',
                                'mentor'         => 'bg-amber-100 text-amber-700',
                                'invite'         => 'bg-purple-100 text-purple-700',
                            ];
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm text-gray-800">{{ $user->nom }} {{ $user->prenom }}</td>
                            <td class="py-3 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="py-3 text-sm text-gray-500">{{ $user->telephone ?? '—' }}</td>
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
                                    <form action="{{ route('admin.toggle', $user) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="text-xs border border-gray-200 rounded-md px-3 py-1.5 text-gray-500
                                                   hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 transition">
                                            {{ $user->active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
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
                            <td colspan="6" class="py-8 text-center text-sm text-gray-400">Aucun utilisateur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

        </main>
    </div>
</div>
</x-app-layout>