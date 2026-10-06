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

    public function indexUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            if ($request->role === 'staff') {
                $query->whereIn('role', ['administrateur', 'responsable']);
            } else {
                $query->where('role', $request->role);
            }
        }

        $users = $query->paginate(10)->withQueryString();

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
        // 1. Validation (Ajout du rôle 'administrateur')
        $request->validate([
            'nom'                => ['required', 'string', 'max:255'],
            'prenom'             => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone'          => ['required', 'string', 'max:20'],
            'poste'              => ['required_if:role,responsable', 'nullable', 'string', 'max:255'],
            'role'               => ['required', 'in:responsable,administrateur,coach,stagiaire'],
            'peut_approuver_tdr' => ['nullable', 'boolean'],
        ]);

        // 2. Génération du mot de passe
        $motDePasse = Str::random(10);
        $nomComplet = trim($request->prenom . ' ' . $request->nom);

        // 3. Création de l'utilisateur
        $user = User::create([
            'nom'                => $request->nom,
            'prenom'             => $request->prenom,
            'email'              => $request->email,
            'telephone'          => $request->telephone,
            'poste'              => $request->poste,
            'role'               => $request->role,
            'active'             => true,
            'peut_approuver_tdr' => $request->boolean('peut_approuver_tdr'),
            'password'           => Hash::make($motDePasse),
        ]);

        // 4. Envoi sécurisé de l'email
        try {
            Mail::to($user->email)->send(
                new NouvelUtilisateurMail($nomComplet, $motDePasse, $user->email)
            );

            return redirect()->route('admin.index')
                             ->with('success', 'Compte créé et mot de passe envoyé avec succès à ' . $user->email);

        } catch (\Exception $e) {
            // Log l'erreur exacte dans storage/logs/laravel.log
            \Log::error("Échec d'envoi du mail de bienvenue à {$user->email} : " . $e->getMessage());

            return redirect()->route('admin.index')
                             ->with('warning', 'Compte créé, mais l\'email n\'a pas pu être envoyé. Vérifiez les logs SMTP.');
        }
    }

    public function toggleStatus(User $user)
    {
        $user->update(['active' => !$user->active]);
        return back()->with('success', $user->active ? 'Compte activé.' : 'Compte désactivé.');
    }

    public function toggleApprobationTdr(User $user)
    {
        $user->update(['peut_approuver_tdr' => !$user->peut_approuver_tdr]);
        return back()->with('success', $user->peut_approuver_tdr
            ? 'Autorisation d\'approuver les TDR accordée à ' . $user->nom . '.'
            : 'Autorisation d\'approuver les TDR retirée à ' . $user->nom . '.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

}