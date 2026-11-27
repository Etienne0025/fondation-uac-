<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Mot de passe oublié ? Indiquez votre adresse e-mail et nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </div>

    {{-- Message de confirmation après l'envoi --}}

    {{-- Erreurs (adresse inconnue, trop de tentatives...) --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm">
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">Retour à la connexion</a>
            <button type="submit"
                class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                Envoyer le lien
            </button>
        </div>
    </form>
</x-guest-layout>