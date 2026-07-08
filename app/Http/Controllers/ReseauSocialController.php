<?php

namespace App\Http\Controllers;

use App\Models\ReseauSocial;
use App\Models\AlerteReseau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReseauSocialController extends Controller
{
    public function index()
    {
        $reseaux  = ReseauSocial::where('user_id', auth()->id())->get();
        $gmail    = $reseaux->where('nomcompte', 'gmail')->first();
        $whatsapp = $reseaux->where('nomcompte', 'whatsapp')->first();

        return view('responsable.reseaux', compact('gmail', 'whatsapp'));
    }

    // Rediriger vers Google OAuth
    public function connecterGmail()
    {
        $params = http_build_query([
            'client_id'     => config('services.google.client_id'),
            'redirect_uri'  => env('GMAIL_REDIRECT_URI'),
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/userinfo.email',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    // Callback Gmail OAuth
    public function callbackGmail(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('reseau.index')
                             ->withErrors(['gmail' => 'Connexion Gmail annulée.']);
        }

        // Échanger le code contre un token
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code'          => $request->code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => env('GMAIL_REDIRECT_URI'),
            'grant_type'    => 'authorization_code',
        ]);

        $token = $response->json();

        if (isset($token['error'])) {
            return redirect()->route('reseau.index')
                             ->withErrors(['gmail' => 'Erreur Gmail : ' . $token['error']]);
        }

        // Récupérer l'email du compte
        $profil = Http::withToken($token['access_token'])
                      ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                      ->json();

        ReseauSocial::updateOrCreate(
            ['user_id' => auth()->id(), 'nomcompte' => 'gmail'],
            [
                'email_compte'  => $profil['email'],
                'access_token'  => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'actif'         => true,
            ]
        );

        return redirect()->route('reseau.index')
                         ->with('success', 'Compte Gmail connecté : ' . $profil['email']);
    }

    // Connecter WhatsApp
   public function connecterWhatsApp(Request $request)
{
    $request->validate([
        'telephone'    => ['required', 'string'],
        'delai_alerte' => ['required', 'integer', 'min:1'],
        'delai_unite'  => ['required', 'in:minutes,heures,jours'],
    ]);

    ReseauSocial::updateOrCreate(
        ['user_id' => auth()->id(), 'nomcompte' => 'whatsapp'],
        [
            'email_compte'  => $profil['email'],
        'access_token'  => $token['access_token'],
        'refresh_token' => $token['refresh_token'] ?? null,
        'actif'         => true,
        'delai_alerte'  => $reseauExistant->delai_alerte ?? 15,      // valeur par défaut si jamais réglée
        'delai_unite'   => $reseauExistant->delai_unite ?? 'minutes',
        ]
    );

    return back()->with('success', 'WhatsApp configuré avec succès.');
}

    // Sauvegarder le délai d'alerte
   public function sauvegarderDelai(Request $request)
{
    $request->validate([
        'nomcompte'    => ['required', 'in:gmail,whatsapp'],
        'delai_alerte' => ['required', 'integer', 'min:1'],
        'delai_unite'  => ['required', 'in:minutes,heures,jours'],
    ]);

    ReseauSocial::updateOrCreate(
        ['user_id' => auth()->id(), 'nomcompte' => $request->nomcompte],
        [
            'delai_alerte' => $request->delai_alerte,
            'delai_unite'  => $request->delai_unite,
        ]
    );

    return back()->with('success', 'Délai d\'alerte mis à jour.');
}

    // Déconnecter un réseau
    public function deconnecter(ReseauSocial $reseau)
    {
        abort_if($reseau->user_id !== auth()->id(), 403);
        $reseau->delete();
        return back()->with('success', 'Compte déconnecté.');
    }

    // Vérifier les messages non lus Gmail
   public function verifierGmail(ReseauSocial $reseau): array
{
    try {
        $accessToken = $reseau->access_token;

        // Rafraîchir le token
        if ($reseau->refresh_token) {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'refresh_token' => $reseau->refresh_token,
                'client_id'     => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'grant_type'    => 'refresh_token',
            ]);
            $newToken = $response->json();
            if (isset($newToken['access_token'])) {
                $accessToken = $newToken['access_token'];
                $reseau->update(['access_token' => $accessToken]);
            }
        }

        // Récupérer les messages non lus
        $response = Http::withToken($accessToken)
            ->get('https://gmail.googleapis.com/gmail/v1/users/me/messages', [
                'q'          => 'is:unread',
                'maxResults' => 5,
            ]);

        $data = $response->json();

        if (isset($data['error'])) {
            \Log::error('Erreur Gmail: ' . $data['error']['message']);
            return ['count' => 0, 'messages' => []];
        }

        $total    = (int) ($data['resultSizeEstimate'] ?? 0);
        $messages = [];

        // Récupérer les détails de chaque message
        foreach ($data['messages'] ?? [] as $msg) {
            $detail = Http::withToken($accessToken)
                ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}", [
                    'format' => 'metadata',
                    'metadataHeaders' => ['From', 'Subject'],
                ]);

            $headers   = $detail->json()['payload']['headers'] ?? [];
            $from      = collect($headers)->firstWhere('name', 'From')['value'] ?? 'Inconnu';
            $subject   = collect($headers)->firstWhere('name', 'Subject')['value'] ?? 'Sans objet';

            $messages[] = [
                'expediteur' => $from,
                'sujet'      => $subject,
            ];
        }

        return ['count' => $total, 'messages' => $messages];

    } catch (\Exception $e) {
        \Log::error('Erreur Gmail: ' . $e->getMessage());
        return ['count' => 0, 'messages' => []];
    }
}

    // Vérifier les messages non lus WhatsApp
   public function verifierWhatsApp(ReseauSocial $reseau): array
{
    try {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $token         = config('services.whatsapp.token');

        $response = Http::withToken($token)
            ->get("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                'status' => 'unread',
            ]);

        $data = $response->json();

        if (isset($data['error'])) {
            \Log::error('Erreur WhatsApp: ' . $data['error']['message']);
            return ['count' => 0, 'messages' => []];
        }

        $messages = [];
        foreach ($data['data'] ?? [] as $msg) {
            $messages[] = [
                'expediteur' => $msg['from'] ?? 'Inconnu',
                'sujet'      => $msg['text']['body'] ?? 'Message WhatsApp',
            ];
        }

        return ['count' => count($data['data'] ?? []), 'messages' => $messages];

    } catch (\Exception $e) {
        \Log::error('Erreur WhatsApp: ' . $e->getMessage());
        return ['count' => 0, 'messages' => []];
    }
}

}