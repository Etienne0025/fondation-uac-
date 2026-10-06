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
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
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
            <h1 class="text-lg font-semibold text-gray-800">Ajouter un utilisateur</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400">{{ now()->translatedFormat('l d F Y') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    Administrateur
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            <div class="max-w-2xl mx-auto">

                <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Retour à la liste
                </a>

                @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.store') }}" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Mot de passe</label>
                            <input type="password" name="password" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Rôle</label>
                            <select name="role" required
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none bg-white">
                                <option value="">-- Choisir --</option>
                                <option value="administrateur" {{ old('role') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                                <option value="responsable" {{ old('role') == 'responsable' ? 'selected' : '' }}>Responsable</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="coach" {{ old('role') == 'coach' ? 'selected' : '' }}>Coach</option>
                                <option value="stagiaire" {{ old('role') == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Poste</label>
                            <input type="text" name="poste" value="{{ old('poste') }}"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-green-600 focus:border-green-600">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="peut_approuver_tdr" value="1" {{ old('peut_approuver_tdr') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">Peut approuver les TDR (Termes De Référence)</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">Compte actif</span>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Créer l'utilisateur
                        </button>
                        <a href="{{ route('admin.index') }}" class="border border-gray-200 text-sm text-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

        </main>
    </div>
</div>
</x-app-layout>