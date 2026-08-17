<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm"
            placeholder="exemple@email.com"
            required
            autofocus
        >
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
        <div class="relative">
            <input
                type="password"
                name="password"
                id="password"
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 outline-none text-sm pr-10"
                placeholder="Votre mot de passe"
                required
            >
            <button
                type="button"
                onclick="togglePassword()"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between text-sm mb-6">
        <label class="flex items-center text-gray-600 cursor-pointer">
            <input
                type="checkbox"
                name="remember"
                class="rounded border-gray-300 text-green-600 focus:ring-green-600"
            >
            <span class="ml-2">Se souvenir de moi</span>
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-green-600 hover:underline">
                Mot de passe oublié ?
            </a>
        @endif
    </div>

    {{-- Bouton Se connecter --}}
    <button
        type="submit"
        class="w-full bg-green-700 text-white py-2.5 rounded-lg font-semibold hover:bg-green-800 transition"
    >
        Se connecter
    </button>

    {{-- Séparateur --}}
    <div class="flex items-center my-4">
        <div class="flex-1 border-t border-gray-200"></div>
        <span class="px-3 text-xs text-gray-400">ou</span>
        <div class="flex-1 border-t border-gray-200"></div>
    </div>

    {{-- Bouton Google --}}
    <a href="{{ route('google.redirect') }}"
       class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Continuer avec Google
    </a>

    <div class="text-center mt-4 text-sm text-gray-500">
        Pas encore de compte ? Veuillez contacter votre administrateur.
    </div>

</form>