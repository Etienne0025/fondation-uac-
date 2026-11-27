@php
    $statut = session('status');

    $messages = collect([
        'success' => session('success'),
        'status'  => match($statut) {
            'profile-updated'        => 'Profil mis à jour.',
            'password-updated'       => 'Mot de passe mis à jour.',
            'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
            default                  => $statut,
        },
        'warning' => session('warning'),
        'error'   => session('error'),
    ])->filter();

    $styles = [
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'status'  => 'bg-green-50 border-green-200 text-green-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'error'   => 'bg-red-50 border-red-200 text-red-800',
    ];
@endphp

@if($messages->isNotEmpty())
<div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-[calc(100%-2rem)] sm:w-96"
     style="margin-top: env(safe-area-inset-top, 0px);">
    @foreach($messages as $type => $message)
    <div x-data="{ show: false }"
         x-init="$nextTick(() => show = true); setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-6"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         role="alert"
         class="flex items-start gap-3 border rounded-lg shadow-lg px-4 py-3 text-sm {{ $styles[$type] }}">
        <p class="flex-1">{{ $message }}</p>
        <button type="button" @click="show = false" class="opacity-60 hover:opacity-100 text-lg leading-none" aria-label="Fermer">&times;</button>
    </div>
    @endforeach
</div>
@endif