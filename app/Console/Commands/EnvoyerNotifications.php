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
    protected $description = 'Envoie les notifications de rappel, de début et d\'alerte pour les activités';

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

                    // Mail de rappel désactivé temporairement — seule la notification push est envoyée.
                    // Pour réactiver : décommenter la ligne ci-dessous.
                    // Mail::to($user->email)->send(new RappelActiviteMail($activite));
                    $user->notify(new \App\Notifications\RappelActiviteNotification($activite, $notification->id));
                    $this->info("Rappel envoyé (push) : {$activite->titre}");

                    $notification->update(['envoye' => true]);

                    // Si l'utilisateur avait cliqué "rappeler" sur une notification précédente
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

                        // Ne reprogrammer que si ça reste avant le début de l'activité
                        if ($prochaineDate->lt($activite->date)) {
                            Notification::create([
                                'activite_id'        => $activite->id,
                                'type'               => 'rappel',
                                'dateenvoi'          => $prochaineDate,
                                'envoye'             => false,
                                'intervalle_valeur'  => $notification->intervalle_valeur,
                                'intervalle_unite'   => $notification->intervalle_unite,
                                'repetition_active'  => true,
                            ]);

                            $this->info("Prochain rappel programmé dans {$notification->intervalle_valeur} {$notification->intervalle_unite}");
                        } else {
                            $this->info("Activité imminente, plus de rappel : {$activite->titre}");
                        }
                    }

                } else {
                    // Activité déjà passée entretemps (statut recalculé selon la date) — pas de rappel
                    $notification->update([
                        'envoye'            => true,
                        'repetition_active' => false,
                    ]);
                    $this->info("Activité déjà passée, rappel annulé : {$activite->titre}");
                }
            }

            // Notification "début" : se déclenche pile à l'heure de l'activité.
            // Pas de condition sur le statut — elle a été programmée précisément
            // pour dateenvoi == date de l'activité, donc elle part telle quelle,
            // et n'est jamais répétée (repetition_active toujours false pour ce type).
            if ($notification->type === 'debut') {
                $user->notify(new \App\Notifications\DebutActiviteNotification($activite, $notification->id));
                $this->info("Notification de début envoyée (push) : {$activite->titre}");

                $notification->update(['envoye' => true]);
            }

            if ($notification->type === 'alerte') {
                // Alertes après activité désactivées temporairement.
                // Les notifications restent en base (envoye = false) mais ne sont plus envoyées.
                // Pour réactiver : supprimer la ligne "continue;" ci-dessous.
                continue;

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