<x-guest-layout>
    <div class="max-w-md mx-auto">

        {{-- Icone --}}
        <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
        </div>

        <h1 class="text-lg font-semibold text-gray-800 mb-1.5">Mot de passe oublié ?</h1>
        <p class="text-sm text-gray-500 mb-6">
            Aucun souci, ça arrive. Indique ton adresse e-mail et on t'envoie un lien pour en choisir un nouveau.
        </p>

        {{-- Message de statut (lien envoye) --}}
        @if (session('status'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-medium text-gray-500 mb-1">Adresse e-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="toi@exemple.com"
                    class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                    @error('email') border-red-300 @enderror">
                @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-green-700 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-green-800 transition">
                Envoyer le lien de réinitialisation
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-green-700 transition inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Retour à la connexion
            </a>
        </div>
    </div>
</x-guest-layout>