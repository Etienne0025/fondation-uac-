<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Notification;
use App\Models\User;
use App\Mail\NouvelUtilisateurMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminController extends BaseActiviteController
{
    protected string $viewPrefix = 'responsable';

    // ===== Gestion utilisateurs =====

    public function indexUsers()
    {
        $users = User::paginate(10);
        $stats = [
            'total'    => User::count(),
            'actifs'   => User::where('active', true)->count(),
            'inactifs' => User::where('active', false)->count(),
            'admins'   => User::where('role', 'administrateur')->count(),
        ];
        return view('admin.users', compact('users', 'stats'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'prenom'    => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string', 'max:20'],
            'poste'     => ['required', 'string', 'max:255'],
            'role'      => ['required', 'in:responsable,mentor,invite'],
        ]);

        $motDePasse = Str::random(10);

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
            new NouvelUtilisateurMail($user->nom . ' ' . $user->prenom, $motDePasse, $user->email)
        );

        return redirect()->route('admin.index')
                         ->with('success', 'Compte créé. Mot de passe envoyé à ' . $user->email);
    }

    public function toggleStatus(User $user)
    {
        $user->update(['active' => !$user->active]);
        return back()->with('success', $user->active ? 'Compte activé.' : 'Compte désactivé.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }


}