<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));

        $expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Vous recevez cet e-mail car une demande de réinitialisation de mot de passe a été faite pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $url)
            ->line("Ce lien expirera dans {$expire} minutes.")
            ->line("Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet e-mail.")
            ->salutation('Cordialement, Startup Valley');
    });
}
}
