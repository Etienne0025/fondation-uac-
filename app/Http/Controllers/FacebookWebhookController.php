<?php

namespace App\Http\Controllers;

use App\Models\ReseauSocial;
use App\Models\InteractionFacebook;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FacebookWebhookController extends Controller
{
    public function verifier(Request $request)
    {
        $challenge   = $request->query('hub_challenge');
        $verifyToken = $request->query('hub_verify_token');

        if ($verifyToken === config('services.facebook.verify_token')) {
            return response($challenge, 200);
        }

        return response('Token invalide', 403);
    }

    public function recevoir(Request $request)
    {
        // Log lisible du payload (utile pour le debug — a retirer ou reduire en production)
        \Illuminate\Support\Facades\Log::info('Webhook Payload Recu :', $request->json()->all() ?: $request->all());

        // 1. Verification de la signature HMAC Facebook
        $signature = $request->header('X-Hub-Signature-256');
        $attendu = 'sha256=' . hash_hmac('sha256', $request->getContent(), config('services.facebook.app_secret'));
        if (!hash_equals($attendu, $signature ?? '')) {
            return response('Signature invalide', 403);
        }

        $payload = $request->json()->all() ?: $request->all();

        foreach ($payload['entry'] ?? [] as $entry) {
            $pageId = $entry['id'] ?? null;

            // ==========================================
            // MESSENGER (Messages prives)
            // ==========================================
            foreach ($entry['messaging'] ?? [] as $event) {
                $reseau = ReseauSocial::where('nomcompte', 'messenger')
                                       ->where('page_id', $pageId)
                                       ->where('actif', true)
                                       ->first();
                if (!$reseau) continue;

                if (isset($event['message']) && empty($event['message']['is_echo'])) {

                    // On enregistre uniquement l'interaction.
                    // L'alerte n'est creee que plus tard par la commande planifiee
                    // facebook:verifier-non-repondus, si le delai configure est depasse
                    // sans reponse (voir $reseau->delai_alerte / delai_unite).
                    InteractionFacebook::updateOrCreate(
                        ['fb_object_id' => $event['message']['mid']],
                        [
                            'reseau_social_id' => $reseau->id,
                            'type'             => 'messenger',
                            'expediteur'       => $event['sender']['id'] ?? 'Inconnu',
                            'contenu'          => $event['message']['text'] ?? '',
                            'recu_a'           => Carbon::createFromTimestampMs($event['timestamp']),
                        ]
                    );

                } elseif (!empty($event['message']['is_echo'])) {
                    // La Page a repondu : on marque l'interaction correspondante comme repondue,
                    // ce qui empeche la commande planifiee de generer une alerte pour elle.
                    InteractionFacebook::where('reseau_social_id', $reseau->id)
                        ->where('expediteur', $event['recipient']['id'] ?? null)
                        ->whereNull('repondu_a')
                        ->update(['repondu_a' => now()]);
                }
            }

            // ==========================================
            // FACEBOOK (Commentaires sur publications)
            // ==========================================
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) !== 'feed') continue;

                $value = $change['value'] ?? [];
                if (($value['item'] ?? null) !== 'comment' || ($value['verb'] ?? null) !== 'add') continue;

                $reseau = ReseauSocial::where('nomcompte', 'facebook')
                                       ->where('page_id', $pageId)
                                       ->where('actif', true)
                                       ->first();
                if (!$reseau) continue;

                $estReponseDeLaPage = ($value['from']['id'] ?? null) === $pageId;

                if ($estReponseDeLaPage && !empty($value['parent_id'])) {
                    // La Page a repondu au commentaire : on marque l'interaction
                    // parente comme repondue.
                    InteractionFacebook::where('fb_object_id', $value['parent_id'])
                        ->update(['repondu_a' => now()]);
                } else {
                    // Nouveau commentaire d'un visiteur : on l'enregistre uniquement.
                    // L'alerte sera creee plus tard par la commande planifiee si
                    // le delai configure est depasse sans reponse.
                    InteractionFacebook::updateOrCreate(
                        ['fb_object_id' => $value['comment_id'] ?? ('comment_' . time())],
                        [
                            'reseau_social_id' => $reseau->id,
                            'type'             => 'facebook',
                            'expediteur'       => $value['from']['name'] ?? 'Inconnu',
                            'contenu'          => $value['message'] ?? '',
                            'recu_a'           => now(),
                        ]
                    );
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}