<x-app-layout>
<div class="flex h-screen bg-gray-100">

    <x-sidebar-admin />

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

            @php
                $roleImpose = request()->query('role'); // 'coach', 'stagiaire', 'staff' ou null
                $estRoleVerrouille = in_array($roleImpose, ['coach', 'stagiaire']);
                $labels = ['coach' => 'Coach', 'stagiaire' => 'Stagiaire'];
            @endphp

            <div class="max-w-2xl bg-white border border-gray-200 rounded-xl p-6">

                <div class="mb-6">
                    <h2 class="text-base font-semibold text-gray-800">Informations du nouvel utilisateur</h2>
                    <p class="text-xs text-gray-400 mt-1">Un mot de passe sera généré et envoyé automatiquement par email.</p>
                </div>

                <form method="POST" action="{{ route('admin.store') }}" id="form-utilisateur">
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

                            @if($estRoleVerrouille)
                                {{-- Rôle imposé par la page d'origine (Coachs / Stagiaires) : pas de choix à faire --}}
                                <div class="w-full p-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-700">
                                    {{ $labels[$roleImpose] }}
                                </div>
                                <input type="hidden" name="role" id="role-select" value="{{ $roleImpose }}">
                            @else
                                <select name="role" id="role-select" onchange="togglePoste()"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm bg-white">
                                    <option value="responsable" {{ old('role', 'responsable') == 'responsable' ? 'selected' : '' }}>Responsable</option>
                                    <option value="administrateur" {{ old('role') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                                    <option value="coach" {{ old('role') == 'coach' ? 'selected' : '' }}>Coach</option>
                                    <option value="stagiaire" {{ old('role') == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                                </select>
                            @endif
                        </div>
                        <div id="poste-container" class="{{ $estRoleVerrouille ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                            <input type="text" name="poste" id="poste-input" value="{{ old('poste') }}"
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
                                placeholder="Ex : Chargé de programme">
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                            Créer le compte et envoyer le mot de passe
                        </button>
                        <a href="{{ route('admin.index', ['role' => $roleImpose]) }}"
                            class="border border-gray-200 text-sm text-gray-500 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>

<script>
function togglePoste() {
    const select = document.getElementById('role-select');
    const container = document.getElementById('poste-container');
    const input = document.getElementById('poste-input');

    // Le poste n'est pertinent (et requis côté serveur) que pour un Responsable.
    if (select.tagName === 'SELECT' && select.value === 'responsable') {
        container.classList.remove('hidden');
        input.setAttribute('required', 'required');
    } else {
        container.classList.add('hidden');
        input.removeAttribute('required');
        input.value = '';
    }
}

document.addEventListener('DOMContentLoaded', togglePoste);
</script>

</x-app-layout>