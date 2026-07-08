<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Gérer la requête entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On vérifie si l'utilisateur est connecté ET s'il est Administrateur
        if (auth()->check() && auth()->user()->role === 'Administrateur') {
            return $next($request);
        }

        // Sinon, on le renvoie vers son tableau de bord avec un message d'erreur
        return redirect('/dashboard')->with('error', 'Accès réservé aux administrateurs.');
    }
}