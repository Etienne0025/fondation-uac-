<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BaseActiviteController;
use App\Http\Controllers\FacebookWebhookController;
use App\Http\Controllers\PlanificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReseauSocialController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\TdrController;
use App\Http\Controllers\WhatsappWebhookController;
use App\Models\AlerteReseau;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pages publiques
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

// ===== Google OAuth (public, limité en fréquence) =====
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| Webhooks Meta (publics, protégés par signature dans les contrôleurs)
| Le CSRF est désactivé pour 'webhook/*' dans bootstrap/app.php
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:120,1')->prefix('webhook')->group(function () {
    Route::get('/whatsapp',  [WhatsappWebhookController::class, 'verifier']);
    Route::post('/whatsapp', [WhatsappWebhookController::class, 'recevoir']);

    Route::get('/facebook',  [FacebookWebhookController::class, 'verifier']);
    Route::post('/facebook', [FacebookWebhookController::class, 'recevoir']);
});

/*
|--------------------------------------------------------------------------
| Utilisateur connecté (tous rôles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =====================================================================
    // ACTIVITÉS : interface commune à tous les rôles
    // - Lecture (index) : tout utilisateur connecté. La visibilité est filtrée
    //   dans BaseActiviteController@index (siennes, publiques, restreintes dont
    //   on est membre).
    // - Création / modification / suppression : rôles listés ci-dessous.
    //   Ajuste la liste (ex. retire 'coach') selon tes règles métier.
    // =====================================================================
    Route::prefix('activites')->name('activites.')->group(function () {
        Route::get('/', [BaseActiviteController::class, 'index'])->name('index');

        Route::middleware('role:administrateur,responsable,coach')->group(function () {
            Route::get('/create',          [BaseActiviteController::class, 'create'])->name('create');
            Route::post('/',               [BaseActiviteController::class, 'store'])->name('store');
            Route::get('/{activite}/edit', [BaseActiviteController::class, 'edit'])->name('edit');
            Route::patch('/{activite}',    [BaseActiviteController::class, 'update'])->name('update');
            Route::delete('/{activite}',   [BaseActiviteController::class, 'destroy'])->name('destroy');
        });
    });

    // Notifications (polling) : ne renvoie que celles de l'utilisateur connecté
    Route::get('/notifications/check', function () {
        try {
            $userId = auth()->id();

            $notifications = Notification::where('envoye', true)
                ->where('vue', false)
                ->whereHas('activite', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->where('statut', '!=', 'valide');
                })
                ->with('activite')
                ->orderBy('updated_at', 'desc')
                ->get();

            $alertesReseau = AlerteReseau::where('user_id', $userId)
                ->where('vue', false)
                ->orderBy('detecte_a', 'desc')
                ->get();

            if ($notifications->isNotEmpty()) {
                Notification::whereIn('id', $notifications->pluck('id'))
                    ->update(['vue' => true]);
            }

            if ($alertesReseau->isNotEmpty()) {
                AlerteReseau::whereIn('id', $alertesReseau->pluck('id'))
                    ->update(['vue' => true]);
            }

            return response()->json([
                'activites' => $notifications,
                'reseaux'   => $alertesReseau,
            ]);
        } catch (\Throwable $e) {
            // Détails dans les logs, message générique pour le client
            Log::error('notifications.check', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    })->name('notifications.check');

    // Marquer toutes les notifications comme lues.
    // Vérifie dans BaseActiviteController que seules les notifications
    // de l'utilisateur connecté sont modifiées.
    Route::post('/notifications/lire', [BaseActiviteController::class, 'marquerToutesLues']);

    // Planification : discussion
    // Vérifie dans storeDiscussionMessage que le rôle de l'utilisateur est autorisé
    // et que le message est validé (longueur, contenu).
    Route::post('/planification/discussion', [PlanificationController::class, 'storeDiscussionMessage'])
        ->name('planification.discussion');

    // Notifications push
    Route::post('/push/subscribe', function (Request $request) {
        $request->validate([
            'endpoint'    => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
        ]);

        $request->user()->updatePushSubscription(
            $request->endpoint,
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );

        return response()->json(['success' => true]);
    })->name('push.subscribe');

    Route::post('/push/unsubscribe', function (Request $request) {
        $request->validate(['endpoint' => 'required|string']);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json(['success' => true]);
    })->name('push.unsubscribe');

    // TDR : les droits (rôle, propriétaire, approbation) sont vérifiés dans TdrController
    Route::prefix('tdr')->name('tdr.')->group(function () {
        Route::get('/',                  [TdrController::class, 'index'])->name('index');
        Route::get('/creer',             [TdrController::class, 'create'])->name('create');
        Route::post('/',                 [TdrController::class, 'store'])->name('store');
        Route::get('/{tdr}',             [TdrController::class, 'show'])->name('show');
        Route::get('/{tdr}/fichier',     [TdrController::class, 'telecharger'])->name('telecharger');
        Route::post('/{tdr}/fichier',    [TdrController::class, 'soumettreFichier'])->middleware('throttle:10,1')->name('fichier');
        Route::post('/{tdr}/avis',       [TdrController::class, 'storeAvis'])->name('avis');
        Route::patch('/{tdr}/approuver', [TdrController::class, 'approuver'])->name('approuver');
        Route::get('/{tdr}/edit',        [TdrController::class, 'edit'])->name('edit');
        Route::patch('/{tdr}',           [TdrController::class, 'update'])->name('update');
        Route::delete('/{tdr}',          [TdrController::class, 'destroy'])->name('destroy');
    });

    // Réseaux sociaux
    // Dans les méthodes callback*, vérifie le paramètre 'state' OAuth
    // et, dans deconnecter(), que le réseau appartient à l'utilisateur connecté.
    Route::prefix('reseau')->name('reseau.')->group(function () {
        Route::get('/',                    [ReseauSocialController::class, 'index'])->name('index');
        Route::get('/gmail/connecter',     [ReseauSocialController::class, 'connecterGmail'])->name('gmail.connecter');
        Route::get('/gmail/callback',      [ReseauSocialController::class, 'callbackGmail'])->name('gmail.callback');
        Route::post('/delai',              [ReseauSocialController::class, 'sauvegarderDelai'])->name('delai');
        Route::post('/whatsapp/connecter', [ReseauSocialController::class, 'connecterWhatsApp'])->name('whatsapp.connecter');

        // Messenger (messages privés de la Page)
        Route::get('/messenger/connecter', [ReseauSocialController::class, 'connecterMessenger'])->name('messenger.connecter');
        Route::get('/messenger/callback',  [ReseauSocialController::class, 'callbackMessenger'])->name('messenger.callback');

        // Facebook (commentaires sur les publications de la Page)
        Route::get('/facebook/connecter',  [ReseauSocialController::class, 'connecterFacebook'])->name('facebook.connecter');
        Route::get('/facebook/callback',   [ReseauSocialController::class, 'callbackFacebook'])->name('facebook.callback');

        // En dernier : route avec paramètre
        Route::delete('/{reseau}',         [ReseauSocialController::class, 'deconnecter'])->name('deconnecter');
    });
});

/*
|--------------------------------------------------------------------------
| PLANIFICATION : administrateur ET responsable
| (URL et nom de route inchangés : /admin/planification, admin.planification,
|  pour ne pas casser les liens existants dans les vues)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:administrateur,responsable'])->group(function () {
    Route::get('/admin/planification', [PlanificationController::class, 'index'])->name('admin.planification');
});

/*
|--------------------------------------------------------------------------
| ADMINISTRATEUR uniquement
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {

    // Gestion des utilisateurs
    Route::get('/users',                 [AdminController::class, 'indexUsers'])->name('index');
    Route::get('/users/create',          [AdminController::class, 'showRegister'])->name('create');
    Route::post('/users',                [AdminController::class, 'storeUser'])->name('store');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleStatus'])->name('toggle');
    Route::patch('/users/{user}/toggle-approbation-tdr', [AdminController::class, 'toggleApprobationTdr'])
        ->name('admin.toggle-approbation-tdr');
    Route::delete('/users/{user}',       [AdminController::class, 'destroyUser'])->name('destroy');

    // Activités (espace admin : validation, gestion globale)
    Route::get('/activites',                      [AdminController::class, 'index'])->name('activites.index');
    Route::get('/activites/create',               [AdminController::class, 'create'])->name('activites.create');
    Route::post('/activites',                     [AdminController::class, 'store'])->name('activites.store');
    Route::get('/activites/{activite}/edit',      [AdminController::class, 'edit'])->name('activites.edit');
    Route::patch('/activites/{activite}',         [AdminController::class, 'update'])->name('activites.update');
    Route::delete('/activites/{activite}',        [AdminController::class, 'destroy'])->name('activites.destroy');
    Route::patch('/activites/{activite}/valider', [AdminController::class, 'valider'])->name('activites.valider');

    // Notifications admin
    Route::get('/notifications',                      [AdminController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/lire-tout',           [AdminController::class, 'marquerToutesLues'])->name('notifications.lire.tout');
    Route::post('/notifications/{notification}/lire', [AdminController::class, 'marquerLue'])->name('notifications.lire');
    Route::delete('/notifications/supprimer',         [AdminController::class, 'supprimerNotifications'])->name('notifications.supprimer');
});

/*
|--------------------------------------------------------------------------
| RESPONSABLE (et ADMINISTRATEUR : l'admin réutilise les vues responsable)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:responsable,administrateur'])->prefix('responsable')->name('responsable.')->group(function () {
    Route::get('/',                          [ResponsableController::class, 'index'])->name('index');
    Route::get('/activites/create',          [ResponsableController::class, 'create'])->name('create');
    Route::post('/activites',                [ResponsableController::class, 'store'])->name('store');
    Route::get('/activites/{activite}/edit', [ResponsableController::class, 'edit'])->name('edit');
    Route::patch('/activites/{activite}',    [ResponsableController::class, 'update'])->name('update');
    Route::delete('/activites/{activite}',   [ResponsableController::class, 'destroy'])->name('destroy');

    Route::get('/notifications',                      [ResponsableController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/lire-tout',           [ResponsableController::class, 'marquerToutesLues'])->name('notifications.lire.tout');
    Route::post('/notifications/{notification}/lire', [ResponsableController::class, 'marquerLue'])->name('notifications.lire');
    Route::delete('/notifications/supprimer',         [ResponsableController::class, 'supprimerNotifications'])->name('notifications.supprimer');
});

/*
|--------------------------------------------------------------------------
| Broadcasting
|--------------------------------------------------------------------------
*/
Broadcast::routes(['middleware' => ['web', 'auth']]);