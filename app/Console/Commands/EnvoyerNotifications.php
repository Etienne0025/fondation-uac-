<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Mail\RappelActiviteMail;
use App\Mail\AlerteActiviteMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnvoyerNotifications extends Command
{
    protected $signature = 'notifications:envoyer';
    protected $description = 'Envoie les notifications de rappel et d\'alerte pour les activités';
public function handle()
{
    $notifications = Notification::where('envoye', false)
        ->where('dateenvoi', '<=', now())
        ->with('activite.user')
        ->get();

    foreach ($notifications as $notification) {
        $activite = $notification->activite;
        $user     = $activite->user;

        if ($notification->type === 'rappel') {
            if ($activite->statut === 'en_attente') {
                Mail::to($user->email)->send(new RappelActiviteMail($activite));
                $this->info("Rappel envoyé : {$activite->titre}");
            }
            $notification->update(['envoye' => true]);
        }

        if ($notification->type === 'alerte') {
            if ($activite->statut !== 'valide') {

                // Marquer comme raté
                $activite->update(['statut' => 'rate']);

                // Envoyer l'alerte
                Mail::to($user->email)->send(new AlerteActiviteMail($activite));
                $this->info("Alerte envoyée : {$activite->titre}");

                // Marquer cette notification comme envoyée
                $notification->update(['envoye' => true]);

                // Si répétition active, créer une nouvelle notification
                if ($notification->repetition_active
                    && $notification->intervalle_valeur
                    && $notification->intervalle_unite
                ) {
                    $prochaineDate = $this->calculerDate(
                        now(),
                        $notification->intervalle_valeur,
                        $notification->intervalle_unite,
                        'apres'
                    );

                    // Créer la prochaine notification
                    Notification::create([
                        'activite_id'        => $activite->id,
                        'type'               => 'alerte',
                        'dateenvoi'          => $prochaineDate,
                        'envoye'             => false,
                        'intervalle_valeur'  => $notification->intervalle_valeur,
                        'intervalle_unite'   => $notification->intervalle_unite,
                        'repetition_active'  => true,
                    ]);

                    $this->info("Prochaine alerte programmée dans {$notification->intervalle_valeur} {$notification->intervalle_unite}");
                }

            } else {
                // Activité déjà validée — arrêter les répétitions
                $notification->update([
                    'envoye'            => true,
                    'repetition_active' => false,
                ]);
                $this->info("Activité validée, alertes arrêtées : {$activite->titre}");
            }
        }
    }

    $this->info("Traitement terminé.");
}

private function calculerDate(\Carbon\Carbon $base, int $valeur, string $unite, string $sens): \Carbon\Carbon
{
    return match($unite) {
        'secondes' => $sens === 'avant' ? $base->copy()->subSeconds($valeur) : $base->copy()->addSeconds($valeur),
        'minutes'  => $sens === 'avant' ? $base->copy()->subMinutes($valeur) : $base->copy()->addMinutes($valeur),
        'heures'   => $sens === 'avant' ? $base->copy()->subHours($valeur)   : $base->copy()->addHours($valeur),
        'jours'    => $sens === 'avant' ? $base->copy()->subDays($valeur)    : $base->copy()->addDays($valeur),
        default    => $base->copy(),
    };
}
}