<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\PlanificationDiscussion;
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

        $dateReference = $request->filled('semaine')
            ? Carbon::parse($request->input('semaine'))
            : Carbon::now();

        $lundi = $dateReference->copy()->startOfWeek(Carbon::MONDAY);

        // Semaine complete : Lundi (0) a Dimanche (6)
        $jours = collect(range(0, 6))->map(function ($i) use ($lundi) {
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
            // Coach / stagiaire : uniquement sa propre ligne.
            $roleFiltre = null;
            $titre = 'Ma planification';
            $membres = User::where('id', $utilisateur->id)->get();
        }

        $debutSemaine = $lundi->copy()->startOfDay();
        $finSemaine   = $lundi->copy()->addDays(6)->endOfDay();

        $activites = Activite::whereBetween('date', [$debutSemaine, $finSemaine])
            ->whereIn('user_id', $membres->pluck('id'))
            ->get();

        $planning = [];
        foreach ($membres as $membre) {
            foreach (range(0, 6) as $i) {
                $planning[$membre->id][$i] = ['matin' => [], 'soir' => []];
            }
        }

        foreach ($activites as $activite) {
            $date = Carbon::parse($activite->date);
            $jourIndex = $lundi->diffInDays($date->copy()->startOfDay());

            if ($jourIndex < 0 || $jourIndex > 6) {
                continue;
            }

            $creneau = $date->hour < 13 ? 'matin' : 'soir';
            $planning[$activite->user_id][$jourIndex][$creneau][] = $activite;
        }

        // Discussion (une seule, partagee) par membre pour la semaine affichee.
        $discussions = PlanificationDiscussion::with('auteur')
            ->where('semaine_debut', $lundi->toDateString())
            ->whereIn('membre_id', $membres->pluck('id'))
            ->orderBy('created_at')
            ->get()
            ->groupBy('membre_id');

        return view('admin.planification.index', compact(
            'membres', 'jours', 'planning', 'lundi', 'titre', 'roleFiltre',
            'discussions', 'accesComplet'
        ));
    }

    public function storeDiscussionMessage(Request $request)
    {
        $utilisateur = auth()->user();

        abort_if(!in_array($utilisateur->role, ['administrateur', 'responsable', 'coach', 'stagiaire']), 403);

        $request->validate([
            'membre_id'     => ['required', 'integer', 'exists:users,id'],
            'semaine_debut' => ['required', 'date'],
            'message'       => ['required', 'string', 'max:2000'],
        ]);

        $accesComplet = in_array($utilisateur->role, ['administrateur', 'responsable']);
        $estSaLigne   = (int) $request->membre_id === $utilisateur->id;

        // Administrateur et responsable peuvent commenter n'importe quelle ligne ;
        // les autres ne peuvent commenter que la leur.
        abort_if(!$accesComplet && !$estSaLigne, 403);

        PlanificationDiscussion::create([
            'membre_id'     => $request->membre_id,
            'user_id'       => $utilisateur->id,
            'semaine_debut' => $request->semaine_debut,
            'message'       => $request->message,
        ]);

        return back()->with('success', 'Message ajouté à la discussion.');
    }
}