<?php

namespace App\Console\Commands;

use App\Models\ReseauSocial;
use App\Models\AlerteReseau;
use App\Models\User;
use App\Notifications\AlerteWebPush;
use App\Http\Controllers\ReseauSocialController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class VerifierReseaux extends Command
{
    protected $signature = 'reseaux:verifier';
    protected $description = 'Vérifie les messages selon l\'intervalle de chaque utilisateur';

    public function handle()
    {
        $reseaux = ReseauSocial::where('actif', true)->get();
        $ctrl = new ReseauSocialController();

        foreach ($reseaux as $reseau) {
            $intervalleMinutes = match($reseau->delai_unite ?? 'minutes') {
                'heures' => ($reseau->delai_alerte ?? 15) * 60,
                'jours'  => ($reseau->delai_alerte ?? 15) * 60 * 24,
                default  => $reseau->delai_alerte ?? 15,
            };

            $dernierCheck = $reseau->derniere_verification ? Carbon::parse($reseau->derniere_verification) : Carbon::now()->subMinutes($intervalleMinutes + 1);

            if ($dernierCheck->addMinutes($intervalleMinutes)->isPast()) {
                $result = ($reseau->nomcompte === 'gmail') ? $ctrl->verifierGmail($reseau) : $ctrl->verifierWhatsApp($reseau);

                if ($result['count'] > 0) {
                    foreach ($result['messages'] as $message) {
                        AlerteReseau::create([
                            'user_id' => $reseau->user_id,
                            'reseau_social_id' => $reseau->id,
                            'type' => $reseau->nomcompte,
                            'expediteur' => $message['expediteur'],
                            'sujet' => $message['sujet'],
                            'messages_non_lus' => $result['count'],
                            'vue' => false,
                            'detecte_a' => now(),
                        ]);

                        // Envoi de la notification push (fonctionne meme si l'utilisateur est hors ligne)
                        $utilisateur = User::find($reseau->user_id);
                        if ($utilisateur) {
                            $utilisateur->notify(new AlerteWebPush(
                                strtoupper($reseau->nomcompte) . ' - Nouveau message',
                                'De: ' . $message['expediteur'],
                                '/responsable/notifications'
                            ));
                        }
                    }
                }
                $reseau->update(['derniere_verification' => now()]);
            }
        }
    }
}