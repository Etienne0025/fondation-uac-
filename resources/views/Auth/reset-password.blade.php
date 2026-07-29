<x-guest-layout>
    <div class="max-w-md mx-auto">

        {{-- Icone --}}
        <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        <h1 class="text-lg font-semibold text-gray-800 mb-1.5">Choisis un nouveau mot de passe</h1>
        <p class="text-sm text-gray-500 mb-6">
            Ton nouveau mot de passe doit être différent des précédents.
        </p>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block text-xs font-medium text-gray-500 mb-1">Adresse e-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                    @error('email') border-red-300 @enderror">
                @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-medium text-gray-500 mb-1">Nouveau mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                    @error('password') border-red-300 @enderror">
                @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-gray-500 mb-1">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                    class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                    @error('password_confirmation') border-red-300 @enderror">
                @error('password_confirmation')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-green-700 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-green-800 transition">
                Réinitialiser le mot de passe
            </button>
        </form>
    </div>
</x-guest-layout>