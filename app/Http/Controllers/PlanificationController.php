<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\PlanificationObservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PlanificationController extends Controller
{
    public function index(Request $request)
    {
        $utilisateur = auth()->user();

        abort_if(!in_array($utilisateur->role, ['administrateur', 'responsable', 'coach', 'stagiaire']), 403);

        // Administrateur et responsable voient tout le monde ; coach et stagiaire ne voient que leur propre ligne.
        $accesComplet = in_array($utilisateur->role, ['administrateur', 'responsable']);

        // Seul l'administrateur peut ecrire dans la colonne "Observation de l'administrateur".
        $peutEditerAdmin = $utilisateur->role === 'administrateur';

        $dateReference = $request->filled('semaine')
            ? Carbon::parse($request->input('semaine'))
            : Carbon::now();

        $lundi = $dateReference->copy()->startOfWeek(Carbon::MONDAY);

        $jours = collect(range(0, 4))->map(function ($i) use ($lundi) {
            return $lundi->copy()->addDays($i);
        });

        if ($accesComplet) {
            $query = User::where('active', true);

            $roleFiltre = $request->query('role');
            $titre = 'Planification — Tous les membres';

            if ($roleFiltre === 'staff') {
                $query->whereIn('role', ['administrateur', 'responsable']);
                $titre = 'Planification — Staff';
            } elseif ($roleFiltre === 'coach') {
                $query->where('role', 'coach');
                $titre = 'Planification — Coachs';
            } elseif ($roleFiltre === 'stagiaire') {
                $query->where('role', 'stagiaire');
                $titre = 'Planification — Stagiaires';
            }

            $membres = $query->orderBy('nom')->orderBy('prenom')->get();
        } else {
            // Coach / stagiaire : uniquement sa propre ligne, aucun filtre de role applicable.
            $roleFiltre = null;
            $titre = 'Ma planification';
            $membres = User::where('id', $utilisateur->id)->get();
        }

        $debutSemaine = $lundi->copy()->startOfDay();
        $finSemaine   = $lundi->copy()->addDays(4)->endOfDay();

        $activites = Activite::whereBetween('date', [$debutSemaine, $finSemaine])
            ->whereIn('user_id', $membres->pluck('id'))
            ->get();

        $planning = [];
        foreach ($membres as $membre) {
            foreach (range(0, 4) as $i) {
                $planning[$membre->id][$i] = ['matin' => [], 'soir' => []];
            }
        }

        foreach ($activites as $activite) {
            $date = Carbon::parse($activite->date);
            $jourIndex = $lundi->diffInDays($date->copy()->startOfDay());

            if ($jourIndex < 0 || $jourIndex > 4) {
                continue;
            }

            $creneau = $date->hour < 13 ? 'matin' : 'soir';
            $planning[$activite->user_id][$jourIndex][$creneau][] = $activite;
        }

        $observations = PlanificationObservation::where('semaine_debut', $lundi->toDateString())
            ->whereIn('user_id', $membres->pluck('id'))
            ->get()
            ->keyBy('user_id');

        return view('admin.planification.index', compact(
            'membres', 'jours', 'planning', 'lundi', 'titre', 'roleFiltre',
            'observations', 'accesComplet', 'peutEditerAdmin'
        ));
    }

    public function updateObservation(Request $request)
    {
        $utilisateur = auth()->user();

        abort_if(!in_array($utilisateur->role, ['administrateur', 'responsable', 'coach', 'stagiaire']), 403);

        $request->validate([
            'user_id'       => ['required', 'integer', 'exists:users,id'],
            'semaine_debut' => ['required', 'date'],
            'champ'         => ['required', 'in:admin,personnelle'],
            'valeur'        => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->champ === 'admin') {
            // Seul l'administrateur peut ecrire l'observation admin, pour n'importe quel membre.
            abort_if($utilisateur->role !== 'administrateur', 403);
        } else {
            // L'observation personnelle ne peut etre modifiee que par son proprietaire.
            abort_if((int) $request->user_id !== $utilisateur->id, 403);
        }

        $champColonne = $request->champ === 'admin' ? 'observation_admin' : 'observation_personnelle';

        PlanificationObservation::updateOrCreate(
            [
                'user_id'       => $request->user_id,
                'semaine_debut' => $request->semaine_debut,
            ],
            [
                $champColonne => $request->valeur,
            ]
        );

        return back()->with('success', 'Observation enregistrée.');
    }
}