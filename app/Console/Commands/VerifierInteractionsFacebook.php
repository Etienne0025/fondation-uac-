<?php

namespace App\Console\Commands;

use App\Models\InteractionFacebook;
use App\Models\AlerteReseau;
use Illuminate\Console\Command;

class VerifierInteractionsFacebook extends Command
{
    protected $signature = 'facebook:verifier-non-repondus';
    protected $description = 'Crée une AlerteReseau pour chaque interaction Facebook/Messenger non répondue au-delà du délai configuré';

    public function handle()
    {
        InteractionFacebook::with('reseau')
            ->whereNull('repondu_a')
            ->whereNull('alerte_envoyee_a')
            ->chunk(50, function ($interactions) {
                foreach ($interactions as $interaction) {
                    $reseau = $interaction->reseau;
                    if (!$reseau) continue;

                    $seuilMinutes = match ($reseau->delai_unite) {
                        'heures' => $reseau->delai_alerte * 60,
                        'jours'  => $reseau->delai_alerte * 1440,
                        default  => $reseau->delai_alerte,
                    };

                    if (now()->diffInMinutes($interaction->recu_a) >= $seuilMinutes) {
                        AlerteReseau::create([
                            'user_id'          => $reseau->user_id,
                            'reseau_social_id' => $reseau->id,
                            'type'             => $interaction->type,
                            'expediteur'       => $interaction->expediteur,
                            'sujet'            => $interaction->contenu,
                            'messages_non_lus' => 1,
                            'vue'              => false,
                            'detecte_a'        => now(),
                        ]);

                        $interaction->update(['alerte_envoyee_a' => now()]);
                    }
                }
            });

        $this->info('Vérification terminée.');
    }
}