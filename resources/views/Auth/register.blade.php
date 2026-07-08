<x-app-layout>
<div class="flex h-screen bg-gray-100">

    {{-- Sidebar --}}
    <aside class="w-60 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-5 border-b border-gray-200 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-semibold text-sm">
                <img src="/image/logo-fuac.jpg" alt="">
            </div>
            <div>
                <p class="font-semibold text-sm text-gray-800">Startup-Valley Agenda</p>
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
            <a href="{{ route('admin.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm bg-green-100 text-green-800 font-medium">
                Ajouter un utilisateur
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
                <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                <div class="flex items-center gap-2 bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-full">
                    Administrateur
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="max-w-2xl bg-white border border-gray-200 rounded-xl p-6">

                <div class="mb-6">
                    <h2 class="text-base font-semibold text-gray-800">Informations du nouvel utilisateur</h2>
                    <p class="text-xs text-gray-400 mt-1">Un mot de passe sera généré et envoyé automatiquement par email.</p>
                </div>

                <form method="POST" action="{{ route('admin.store') }}">
                    @csrf

                    {{-- Ligne 1 : Nom et Prénom --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="Dupont" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="Jean" required>
                        </div>
                    </div>

                    {{-- Ligne 2 : Email et Téléphone --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="exemple@email.com" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" name="telephone" value="{{ old('telephone') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="01 97 000 000" required>
                        </div>
                    </div>

                    {{-- Ligne 3 : Rôle et Poste --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            <select name="role"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                                <option value="Responsable" {{ old('role', 'Responsable') == 'Responsable' ? 'selected' : '' }}>Responsable</option>
                                <option value="Administrateur" {{ old('role') == 'Administrateur' ? 'selected' : '' }}>Administrateur</option>
                               
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                            <input type="text" name="poste" value="{{ old('poste') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="Ex : Chargé de programme" required>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Créer le compte et envoyer le mot de passe
                        </button>
                        <a href="{{ route('admin.index') }}"
                            class="border border-gray-200 text-sm text-gray-500 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>
</x-app-layout>