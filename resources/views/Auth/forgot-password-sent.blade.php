<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto w-14 h-14 rounded-full bg-green-100 text-green-700 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>

        <h1 class="text-lg font-semibold text-gray-800 mb-2">E-mail envoyé</h1>

        <p class="text-sm text-gray-600">
            Un lien de réinitialisation a été envoyé
            @if(request('email'))
                à <span class="font-medium text-gray-800">{{ request('email') }}</span>.
            @else
                à votre adresse e-mail.
            @endif
            Ouvrez votre boîte de réception (et vérifiez les courriers indésirables), puis cliquez sur le lien.
        </p>

        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('login') }}"
                class="bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-800 transition">
                Retour à la connexion
            </a>
            <a href="{{ route('password.request') }}"
                class="border border-gray-200 text-sm text-gray-500 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">
                Renvoyer le lien
            </a>
        </div>
    </div>
</x-guest-layout>