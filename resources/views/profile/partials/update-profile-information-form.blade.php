<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-3.5">
    @csrf
    @method('patch')

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="nom" class="block text-xs font-medium text-gray-500 mb-1">Nom</label>
            <input id="nom" name="nom" type="text" value="{{ old('nom', $user->nom) }}" required autofocus autocomplete="family-name"
                class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                @error('nom') border-red-300 @enderror">
            @error('nom')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="prenom" class="block text-xs font-medium text-gray-500 mb-1">Prénom</label>
            <input id="prenom" name="prenom" type="text" value="{{ old('prenom', $user->prenom) }}" required autocomplete="given-name"
                class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
                @error('prenom') border-red-300 @enderror">
            @error('prenom')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="email" class="block text-xs font-medium text-gray-500 mb-1">Adresse e-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
            @error('email') border-red-300 @enderror">
        @error('email')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="mt-2.5">
            <p class="text-xs text-gray-500">
                Ton adresse e-mail n'est pas vérifiée.
                <button form="send-verification" class="underline text-gray-600 hover:text-green-700 transition">
                    Renvoyer l'e-mail de vérification.
                </button>
            </p>

            @if (session('status') === 'verification-link-sent')
            <p class="mt-2 text-xs font-medium text-green-700">
                Un nouveau lien de vérification a été envoyé à ton adresse e-mail.
            </p>
            @endif
        </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
            Enregistrer
        </button>

        @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
            class="text-sm text-green-700 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            Enregistré.
        </p>
        @endif
    </div>
</form>