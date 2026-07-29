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
        $reseaux   = ReseauSocial::where('user_id', auth()->id())->get();
        $gmail     = $reseaux->where('nomcompte', 'gmail')->first();
        $whatsapp  = $reseaux->where('nomcompte', 'whatsapp')->first();
        $messenger = $reseaux->where('nomcompte', 'messenger')->first();
        $facebook  = $reseaux->where('nomcompte', 'facebook')->first();

        return view('responsable.reseaux', compact('gmail', 'whatsapp', 'messenger', 'facebook'));
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

        $profil = Http::withToken($token['access_token'])
                      ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                      ->json();

        $reseauExistant = ReseauSocial::where('user_id', auth()->id())
                                       ->where('nomcompte', 'gmail')
                                       ->first();

        ReseauSocial::updateOrCreate(
            ['user_id' => auth()->id(), 'nomcompte' => 'gmail'],
            [
                'email_compte'  => $profil['email'],
                'access_token'  => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'actif'         => true,
                'delai_alerte'  => $reseauExistant->delai_alerte ?? 15,
                'delai_unite'   => $reseauExistant->delai_unite ?? 'minutes',
            ]
        );

        return redirect()->route('reseau.index')
                         ->with('success', 'Compte Gmail connecté : ' . $profil['email']);
    }

    // Connecter WhatsApp (simple formulaire : numéro + délai d'alerte)
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
                'telephone_compte' => $request->telephone,
                'delai_alerte'     => $request->delai_alerte,
                'delai_unite'      => $request->delai_unite,
                'actif'            => true,
            ]
        );

        return back()->with('success', 'WhatsApp configuré avec succès.');
    }

    // Sauvegarder le délai d'alerte
    public function sauvegarderDelai(Request $request)
    {
        $request->validate([
            'nomcompte'    => ['required', 'in:gmail,whatsapp,messenger,facebook'],
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

    // --- Messenger ---

    public function connecterMessenger()
    {
        $params = http_build_query([
            'client_id'     => config('services.facebook.app_id'),
            'redirect_uri'  => config('services.facebook.messenger_redirect'),
            'response_type' => 'code',
            'scope'         => 'pages_show_list,pages_messaging,pages_manage_metadata',
        ]);

        return redirect('https://www.facebook.com/v19.0/dialog/oauth?' . $params);
    }

    public function callbackMessenger(Request $request)
    {
        return $this->traiterCallbackFacebook($request, 'messenger', config('services.facebook.messenger_redirect'), 'messages');
    }

    // --- Facebook (commentaires) ---

    public function connecterFacebook()
    {
        $params = http_build_query([
            'client_id'     => config('services.facebook.app_id'),
            'redirect_uri'  => config('services.facebook.facebook_redirect'),
            'response_type' => 'code',
            'scope'         => 'pages_show_list,pages_read_engagement,pages_manage_metadata',
        ]);

        return redirect('https://www.facebook.com/v19.0/dialog/oauth?' . $params);
    }

    public function callbackFacebook(Request $request)
    {
        return $this->traiterCallbackFacebook($request, 'facebook', config('services.facebook.facebook_redirect'), 'feed');
    }

    // --- Logique commune ---

    private function traiterCallbackFacebook(Request $request, string $nomcompte, string $redirectUri, string $champWebhook)
    {
        if ($request->has('error')) {
            return redirect()->route('reseau.index')
                             ->withErrors([$nomcompte => 'Connexion Facebook annulée.']);
        }

        $tokenResp = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'client_id'     => config('services.facebook.app_id'),
            'client_secret' => config('services.facebook.app_secret'),
            'redirect_uri'  => $redirectUri,
            'code'          => $request->code,
        ]);
        $userToken = $tokenResp->json()['access_token'] ?? null;

        if (!$userToken) {
            \Log::error('Erreur token Facebook (' . $nomcompte . '): ' . $tokenResp->body());
            return redirect()->route('reseau.index')
                             ->withErrors([$nomcompte => 'Erreur lors de la connexion Facebook.']);
        }

        $pagesResp = Http::get('https://graph.facebook.com/v19.0/me/accounts', [
    'access_token' => $userToken,
    'fields'       => 'id,name,access_token,category',
]);

\Log::info('Réponse /me/accounts (' . $nomcompte . '): ' . $pagesResp->body());

$page = $pagesResp->json()['data'][0] ?? null;
        if (!$page) {
            \Log::error('Erreur pages Facebook (' . $nomcompte . '): ' . $pagesResp->body());
            return redirect()->route('reseau.index')
                             ->withErrors([$nomcompte => 'Aucune Page Facebook trouvée pour ce compte.']);
        }

        $reseauExistant = ReseauSocial::where('user_id', auth()->id())
                                       ->where('nomcompte', $nomcompte)
                                       ->first();

        ReseauSocial::updateOrCreate(
            ['user_id' => auth()->id(), 'nomcompte' => $nomcompte],
            [
                'page_id'      => $page['id'],
                'nom_page'     => $page['name'],
                'access_token' => $page['access_token'],
                'actif'        => true,
                'delai_alerte' => $reseauExistant->delai_alerte ?? 30,
                'delai_unite'  => $reseauExistant->delai_unite ?? 'minutes',
            ]
        );

        $subResp = Http::post("https://graph.facebook.com/v19.0/{$page['id']}/subscribed_apps", [
            'subscribed_fields' => $champWebhook,
            'access_token'      => $page['access_token'],
        ]);

        if (!$subResp->successful() || !($subResp->json()['success'] ?? false)) {
            \Log::error('Erreur abonnement webhook Facebook (' . $nomcompte . '): ' . $subResp->body());
            return redirect()->route('reseau.index')
                             ->with('success', 'Page Facebook connectée : ' . $page['name'])
                             ->withErrors([$nomcompte => 'Connecté, mais l\'abonnement au webhook a échoué — voir les logs.']);
        }

        return redirect()->route('reseau.index')
                         ->with('success', 'Page Facebook connectée : ' . $page['name']);
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

                $headers = $detail->json()['payload']['headers'] ?? [];
                $from    = collect($headers)->firstWhere('name', 'From')['value'] ?? 'Inconnu';
                $subject = collect($headers)->firstWhere('name', 'Subject')['value'] ?? 'Sans objet';

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