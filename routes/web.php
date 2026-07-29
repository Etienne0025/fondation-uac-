<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\ReseauSocialController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Http\Controllers\FacebookWebhookController;
use App\Http\Controllers\WhatsappWebhookController;
use App\Http\Controllers\BaseActiviteController;
use App\Http\Controllers\PlanificationController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// ===== Routes Admin =====
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Gestion utilisateurs
    Route::get('/users',                        [AdminController::class, 'indexUsers'])    ->name('index'); // Corrigé ici
    Route::get('/users/create',                 [AdminController::class, 'showRegister'])  ->name('create');
    Route::post('/users',                       [AdminController::class, 'storeUser'])     ->name('store');
    Route::patch('/users/{user}/toggle',        [AdminController::class, 'toggleStatus'])  ->name('toggle');
    Route::delete('/users/{user}',              [AdminController::class, 'destroyUser'])   ->name('destroy');
    Route::get('/planification', [PlanificationController::class, 'index'])->name('planification');
    // Activités admin (Correction : utilisation des noms de méthodes réels)
    Route::get('/activites',                    [AdminController::class, 'index'])        ->name('activites.index');
    Route::get('/activites/create',             [AdminController::class, 'create'])       ->name('activites.create');
    Route::post('/activites',                   [AdminController::class, 'store'])        ->name('activites.store');
    Route::get('/activites/{activite}/edit',    [AdminController::class, 'edit'])         ->name('activites.edit');
    Route::patch('/activites/{activite}',       [AdminController::class, 'update'])       ->name('activites.update');
    Route::delete('/activites/{activite}',      [AdminController::class, 'destroy'])      ->name('activites.destroy');
    Route::patch('/activites/{activite}/valider', [AdminController::class, 'valider'])    ->name('activites.valider');

    // Notifications admin
    Route::get('/notifications',                [AdminController::class, 'notifications']) ->name('notifications');
    Route::post('/notifications/{notification}/lire', [AdminController::class, 'marquerLue'])  ->name('notifications.lire');
    Route::post('/notifications/lire-tout',     [AdminController::class, 'marquerToutesLues'])->name('notifications.lire.tout');
    Route::delete('/notifications/supprimer', [AdminController::class, 'supprimerNotifications'])->name('notifications.supprimer');
});
// ===== Routes Responsable =====
Route::middleware(['auth'])->prefix('responsable')->name('responsable.')->group(function () {
    Route::get('/',                               [ResponsableController::class, 'index'])           ->name('index');
    Route::get('/activites/create',               [ResponsableController::class, 'create'])          ->name('create');
    Route::post('/activites',                     [ResponsableController::class, 'store'])            ->name('store');
    Route::get('/activites/{activite}/edit',      [ResponsableController::class, 'edit'])             ->name('edit');
    Route::patch('/activites/{activite}',         [ResponsableController::class, 'update'])           ->name('update');
    Route::delete('/activites/{activite}',        [ResponsableController::class, 'destroy'])          ->name('destroy');
    Route::patch('/activites/{activite}/valider', [ResponsableController::class, 'valider'])          ->name('valider');
    Route::get('/notifications',                  [ResponsableController::class, 'notifications'])    ->name('notifications');
    Route::post('/notifications/{notification}/lire', [ResponsableController::class, 'marquerLue'])   ->name('notifications.lire');
    Route::post('/notifications/lire-tout',       [ResponsableController::class, 'marquerToutesLues'])->name('notifications.lire.tout');
    Route::delete('/notifications/supprimer', [ResponsableController::class, 'supprimerNotifications'])->name('notifications.supprimer');
});

// ===== Réseaux sociaux =====
Route::middleware(['auth'])->prefix('reseau')->name('reseau.')->group(function () {
    Route::get('/',                    [ReseauSocialController::class, 'index'])           ->name('index');
    Route::get('/gmail/connecter',     [ReseauSocialController::class, 'connecterGmail'])  ->name('gmail.connecter');
    Route::get('/gmail/callback',      [ReseauSocialController::class, 'callbackGmail'])   ->name('gmail.callback');
    Route::post('/delai',              [ReseauSocialController::class, 'sauvegarderDelai'])->name('delai');
    Route::post('/whatsapp/connecter', [ReseauSocialController::class, 'connecterWhatsApp'])->name('whatsapp.connecter');
    Route::delete('/{reseau}',         [ReseauSocialController::class, 'deconnecter'])     ->name('deconnecter');

    // Messenger (messages privés de la Page)
    Route::get('/messenger/connecter', [ReseauSocialController::class, 'connecterMessenger'])->name('messenger.connecter');
    Route::get('/messenger/callback',  [ReseauSocialController::class, 'callbackMessenger']) ->name('messenger.callback');

    // Facebook (commentaires sur les publications de la Page)
    Route::get('/facebook/connecter',  [ReseauSocialController::class, 'connecterFacebook']) ->name('facebook.connecter');
    Route::get('/facebook/callback',   [ReseauSocialController::class, 'callbackFacebook'])  ->name('facebook.callback');
});

// ===== Google OAuth =====
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ===== Notifications polling =====
Route::middleware(['auth'])->get('/notifications/check', function () {
    try {
        $userId = auth()->id();

        $notifications = App\Models\Notification::where('envoye', true)
            ->where('vue', false)
            ->whereHas('activite', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('statut', '!=', 'valide');
            })
            ->with('activite')
            ->orderBy('updated_at', 'desc')
            ->get();

        $alertesReseau = App\Models\AlerteReseau::where('user_id', $userId)
            ->where('vue', false)
            ->orderBy('detecte_a', 'desc')
            ->get();

        if ($notifications->count() > 0) {
            App\Models\Notification::whereIn('id', $notifications->pluck('id'))
                ->update(['vue' => true]);
        }

        if ($alertesReseau->count() > 0) {
            App\Models\AlerteReseau::whereIn('id', $alertesReseau->pluck('id'))
                ->update(['vue' => true]);
        }

        return response()->json([
            'activites' => $notifications,
            'reseaux'   => $alertesReseau,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('notifications.check');

// ===== Broadcasting =====
Broadcast::routes(['middleware' => ['web', 'auth']]);


Route::post('/notifications/lire', [BaseActiviteController::class, 'marquerToutesLues']);

//use App\Http\Controllers\BaseActiviteController;

Route::get('/webhook/whatsapp', [WhatsappWebhookController::class, 'verifier']);
Route::post('/webhook/whatsapp', [WhatsappWebhookController::class, 'recevoir']);

Route::withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->group(function () {
    Route::get('/webhook/facebook',  [FacebookWebhookController::class, 'verifier']);
    Route::post('/webhook/facebook', [FacebookWebhookController::class, 'recevoir']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/planification/observation', [PlanificationController::class, 'updateObservation'])->name('planification.observation');
});