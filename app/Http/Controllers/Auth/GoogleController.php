<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Rediriger vers Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback après connexion Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Chercher si l'utilisateur existe déjà
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if ($user) {
                // Mettre à jour le google_id si pas encore enregistré
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // Créer le compte automatiquement
                $user = User::create([
                    'nom'       => $googleUser->getName(),
                    'prenom'    => '',
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'role'      => 'invite',
                    'active'    => true,
                    'password'  => bcrypt(\Illuminate\Support\Str::random(16)),
                ]);
            }

            // Vérifier si le compte est actif
            if (!$user->active) {
                return redirect()->route('login')
                                 ->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administrateur.']);
            }

            Auth::login($user);

            // Redirection selon le rôle
            if ($user->role === 'administrateur') {
                return redirect()->route('admin.index');
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')
                             ->withErrors(['email' => 'Erreur de connexion avec Google. Réessayez.']);
        }
    }
}