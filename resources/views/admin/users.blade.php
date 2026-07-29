<x-app-layout>
<div class="flex h-screen bg-gray-100">

   <x-sidebar-admin />
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
    <div class="flex gap-2">
        <a href="{{ route('admin.planification', ['role' => request('role')]) }}"
            class="flex items-center gap-1.5 border border-gray-200 text-gray-600 text-xs font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            Voir la planification
        </a>
       <a href="{{ route('admin.create', ['role' => request('role')]) }}"
    class="flex items-center gap-1.5 bg-green-700 text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    Ajouter un utilisateur
</a>
    </div>
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