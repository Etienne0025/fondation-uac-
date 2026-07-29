<div class="space-y-4">

    <p class="text-sm text-gray-600">
        Une fois ton compte supprimé, toutes les données et ressources associées seront définitivement effacées.
        Avant de continuer, pense à télécharger toute information que tu souhaites conserver.
    </p>

    <button type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        Supprimer mon compte
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-800">
                Confirmer la suppression du compte ?
            </h2>

            <p class="mt-1.5 text-sm text-gray-500">
                Cette action est irréversible. Saisis ton mot de passe pour confirmer la suppression définitive de ton compte.
            </p>

            <div class="mt-5">
                <label for="password" class="sr-only">Mot de passe</label>
                <input id="password" name="password" type="password" placeholder="Mot de passe"
                    class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                @error('password', 'userDeletion')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')"
                    class="border border-gray-200 text-sm text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Supprimer définitivement
                </button>
            </div>
        </form>
    </x-modal>
</div>