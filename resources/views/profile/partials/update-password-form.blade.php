<form method="post" action="{{ route('password.update') }}" class="space-y-3.5">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password" class="block text-xs font-medium text-gray-500 mb-1">Mot de passe actuel</label>
        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
            @error('current_password', 'updatePassword') border-red-300 @enderror">
        @error('current_password', 'updatePassword')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password" class="block text-xs font-medium text-gray-500 mb-1">Nouveau mot de passe</label>
        <input id="update_password_password" name="password" type="password" autocomplete="new-password"
            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
            @error('password', 'updatePassword') border-red-300 @enderror">
        @error('password', 'updatePassword')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="update_password_password_confirmation" class="block text-xs font-medium text-gray-500 mb-1">Confirmer le nouveau mot de passe</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 transition
            @error('password_confirmation', 'updatePassword') border-red-300 @enderror">
        @error('password_confirmation', 'updatePassword')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-800 transition">
            Enregistrer
        </button>

        @if (session('status') === 'password-updated')
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