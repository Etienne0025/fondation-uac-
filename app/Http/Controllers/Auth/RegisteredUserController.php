<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NouvelUtilisateurMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'prenom'    => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string', 'max:20'],
            'poste'     => ['required_if:role,Administrateur', 'string', 'max:255'],
            'role'      => ['required', 'in:responsable,Administrateur'],
        ]);

        // Générer un mot de passe aléatoire
        $motDePasse = Str::random(10);

        // Créer l'utilisateur
        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'poste'     => $request->poste,
            'role'      => $request->role,
            'active'    => true,
            'password'  => Hash::make($motDePasse),
        ]);

        
        Mail::to($user->email)->send(
            new NouvelUtilisateurMail($user->nom . ' ' . $user->prenom, $motDePasse)
        );

        
        return redirect()->route('admin.index')
                         ->with('success', 'Compte créé. Le mot de passe a été envoyé à ' . $user->email);
    }
}