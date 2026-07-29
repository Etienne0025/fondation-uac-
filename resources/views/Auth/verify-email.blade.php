<x-guest-layout>
    <div class="max-w-md mx-auto">

        {{-- Icone --}}
        <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
        </div>

        <h1 class="text-lg font-semibold text-gray-800 mb-1.5">Vérifie ton adresse e-mail</h1>
        <p class="text-sm text-gray-500 mb-6">
            Merci de t'être inscrit ! Avant de continuer, clique sur le lien qu'on vient de t'envoyer par e-mail pour confirmer ton adresse.
            Tu ne l'as pas reçu ? On peut t'en renvoyer un.
        </p>

        @if (session('status') == 'verification-link-sent')
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
            Un nouveau lien de vérification a été envoyé à l'adresse indiquée lors de ton inscription.
        </div>
        @endif

        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
                    Renvoyer l'e-mail
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-green-700 transition underline">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>