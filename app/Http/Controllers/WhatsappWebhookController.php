<?php

namespace App\Http\Controllers;

use App\Models\ReseauSocial;
use App\Models\AlerteReseau;
use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    /**
     * Etape 1 : Meta appelle cette route en GET pour verifier l'URL du webhook.
     */
    public function verifier(Request $request)
    {
        $challenge   = $request->query('hub_challenge');
        $verifyToken = $request->query('hub_verify_token');

        if ($verifyToken === env('FUAC_WHATSAPP_VERIFY_TOKEN')) {
            return response($challenge, 200);
        }

        return response('Token invalide', 403);
    }

    /**
     * Etape 2 : Meta appelle cette route en POST a chaque message entrant.
     */
    public function recevoir(Request $request)
    {
        $data = $request->all();

        foreach ($data['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                foreach ($value['messages'] ?? [] as $message) {
                    $numeroBrut = $message['from'] ?? null;

                    if (!$numeroBrut) {
                        continue;
                    }

                    $texte = $message['text']['body'] ?? ('Message WhatsApp (' . ($message['type'] ?? 'inconnu') . ')');
                    $numeroNormalise = $this->normaliserNumero($numeroBrut);

                    $reseau = ReseauSocial::where('nomcompte', 'whatsapp')
                        ->where('actif', true)
                        ->get()
                        ->first(fn ($r) => $this->normaliserNumero($r->telephone_compte) === $numeroNormalise);

                    if (!$reseau) {
                        \Log::warning("Webhook WhatsApp : aucun utilisateur pour le numero {$numeroBrut}");
                        continue;
                    }

                    AlerteReseau::create([
                        'user_id'          => $reseau->user_id,
                        'reseau_social_id' => $reseau->id,
                        'type'             => 'whatsapp',
                        'expediteur'       => $numeroBrut,
                        'sujet'            => $texte,
                        'messages_non_lus' => 1,
                        'vue'              => false,
                        'detecte_a'        => now(),
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Retire tout ce qui n'est pas un chiffre pour comparer deux numeros
     * independamment du format (+, espaces, 00, etc.).
     */
    private function normaliserNumero(?string $numero): string
    {
        return preg_replace('/\D+/', '', $numero ?? '');
    }
}