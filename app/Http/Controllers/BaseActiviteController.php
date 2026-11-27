<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class BaseActiviteController extends Controller
{
    // Préfixe des vues, surchargeable dans les contrôleurs enfants
    protected string $viewPrefix = 'activites';

    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Activite::with(['acces', 'user'])
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('acces', function($q2) use ($userId) {
                      $q2->where('droitacces', 'public')
                         ->orWhere(function($q3) use ($userId) {
                             $q3->where('droitacces', 'restreint')
                                ->whereHas('membres', function($q4) use ($userId) {
                                    $q4->where('users.id', $userId);
                                });
                         });
                  });
            });

        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }

        // Le statut n'est plus une colonne en base : on filtre directement sur la date
        if ($request->filled('statut')) {
            if ($request->statut === 'en_attente') {
                $query->where('date', '>', now());
            } elseif ($request->statut === 'passee') {
                $query->where('date', '<=', now());
            }
        }

        $activites = $query->latest('date')->paginate(10)->withQueryString();

        // Même filtre de visibilité que la liste ci-dessus, correctement parenthésé
        // (le bug précédent : ->orWhereHas() non englobé faisait que le AND ->where('date', ...)
        // ne s'appliquait qu'à la branche "public", pas à la branche "user_id" — du coup
        // toutes tes propres activités passées étaient comptées comme "en attente")
        $baseStatsQuery = function () use ($userId) {
            return Activite::where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('acces', function($q2) use ($userId) {
                      $q2->where('droitacces', 'public')
                         ->orWhere(function($q3) use ($userId) {
                             $q3->where('droitacces', 'restreint')
                                ->whereHas('membres', function($q4) use ($userId) {
                                    $q4->where('users.id', $userId);
                                });
                         });
                  });
            });
        };

        $stats = [
            'total'      => $baseStatsQuery()->count(),
            'en_attente' => $baseStatsQuery()->where('date', '>', now())->count(),
        ];

        return view($this->viewPrefix . '.index', compact('activites', 'stats'));
    }

    public function create()
    {
        $utilisateurs = User::where('active', true)
            ->where('id', '!=', auth()->id())
            ->orderBy('nom')
            ->get();

        return view($this->viewPrefix . '.create', compact('utilisateurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'                       => ['required', 'string', 'max:255'],
            'description'                 => ['required', 'string'],
            'date'                        => ['required', 'date'],
            'lieu'                        => ['required', 'string', 'max:255'],
            'droitacces'                  => ['required', 'in:prive,public,restreint'],
            'membres'                     => ['required_if:droitacces,restreint', 'array'],
            'membres.*'                   => ['integer', 'exists:users,id'],
            'rappels_avant_valeur'        => ['nullable', 'array'],
            'rappels_avant_valeur.*'      => ['integer', 'min:1'],
            'rappels_avant_unite'         => ['nullable', 'array'],
            'rappels_avant_unite.*'       => ['in:secondes,minutes,heures,jours'],
            'alertes_apres_valeur'        => ['nullable', 'array'],
            'alertes_apres_valeur.*'      => ['integer', 'min:1'],
            'alertes_apres_unite'         => ['nullable', 'array'],
            'alertes_apres_unite.*'       => ['in:secondes,minutes,heures,jours'],
            'alertes_intervalle_valeur'   => ['nullable', 'array'],
            'alertes_intervalle_valeur.*' => ['integer', 'min:1'],
            'alertes_intervalle_unite'    => ['nullable', 'array'],
            'alertes_intervalle_unite.*'  => ['in:secondes,minutes,heures,jours'],
        ]);

        $rappelsAvant = [];
        foreach ($request->rappels_avant_valeur ?? [] as $i => $valeur) {
            $rappelsAvant[] = [
                'valeur' => (int) $valeur,
                'unite'  => $request->rappels_avant_unite[$i] ?? 'heures',
            ];
        }

        $alertesApres = [];
        foreach ($request->alertes_apres_valeur ?? [] as $i => $valeur) {
            $alertesApres[] = [
                'valeur'            => (int) $valeur,
                'unite'             => $request->alertes_apres_unite[$i] ?? 'heures',
                'intervalle_valeur' => (int) ($request->alertes_intervalle_valeur[$i] ?? 1),
                'intervalle_unite'  => $request->alertes_intervalle_unite[$i] ?? 'heures',
            ];
        }

        $activite = Activite::create([
            'user_id'        => auth()->id(),
            'titre'          => $request->titre,
            'description'    => $request->description,
            'date'           => $request->date,
            'lieu'           => $request->lieu,
            'rappels_avant'  => $rappelsAvant,
            'alertes_apres'  => $alertesApres,
        ]);

        $acces = $activite->acces()->create(['droitacces' => $request->droitacces]);

        if ($request->droitacces === 'restreint') {
            $acces->membres()->sync($request->membres ?? []);
        }

        $dateEvenement = \Carbon\Carbon::parse($request->date);

        foreach ($rappelsAvant as $r) {
            $activite->notifications()->create([
                'type'              => 'rappel',
                'dateenvoi'         => $this->calculerDate($dateEvenement, $r['valeur'], $r['unite'], 'avant'),
                'envoye'            => false,
                'repetition_active' => false,
            ]);
        }

        foreach ($alertesApres as $a) {
            $activite->notifications()->create([
                'type'               => 'alerte',
                'dateenvoi'          => $this->calculerDate($dateEvenement, $a['valeur'], $a['unite'], 'apres'),
                'envoye'             => false,
                'intervalle_valeur'  => $a['intervalle_valeur'],
                'intervalle_unite'   => $a['intervalle_unite'],
                'repetition_active'  => true,
            ]);
        }

        // Notification "début" : se déclenche pile à l'heure de l'activité,
        // indépendamment des rappels_avant/alertes_apres configurés par l'utilisateur.
        $activite->notifications()->create([
            'type'              => 'debut',
            'dateenvoi'         => $dateEvenement,
            'envoye'            => false,
            'repetition_active' => false,
        ]);

        return redirect()->route($this->routePrefix() . '.index')
                         ->with('success', 'Activité créée avec succès.');
    }

    public function edit(Activite $activite)
    {
        abort_if(
            $activite->user_id !== auth()->id() && auth()->user()->role !== 'administrateur',
            403
        );

        $utilisateurs = User::where('active', true)
            ->where('id', '!=', auth()->id())
            ->orderBy('nom')
            ->get();

        $membresSelectionnes = $activite->acces?->membres->pluck('id')->toArray() ?? [];

        return view($this->viewPrefix . '.edit', compact('activite', 'utilisateurs', 'membresSelectionnes'));
    }

    public function update(Request $request, Activite $activite)
    {
        abort_if(
            $activite->user_id !== auth()->id() && auth()->user()->role !== 'administrateur',
            403
        );

        $request->validate([
            'titre'                       => ['required', 'string', 'max:255'],
            'description'                 => ['required', 'string'],
            'date'                        => ['required', 'date'],
            'lieu'                        => ['required', 'string', 'max:255'],
            'droitacces'                  => ['required', 'in:prive,public,restreint'],
            'membres'                     => ['required_if:droitacces,restreint', 'array'],
            'membres.*'                   => ['integer', 'exists:users,id'],
            'rappels_avant_valeur'        => ['nullable', 'array'],
            'rappels_avant_valeur.*'      => ['integer', 'min:1'],
            'rappels_avant_unite'         => ['nullable', 'array'],
            'rappels_avant_unite.*'       => ['in:secondes,minutes,heures,jours'],
            'alertes_apres_valeur'        => ['nullable', 'array'],
            'alertes_apres_valeur.*'      => ['integer', 'min:1'],
            'alertes_apres_unite'         => ['nullable', 'array'],
            'alertes_apres_unite.*'       => ['in:secondes,minutes,heures,jours'],
            'alertes_intervalle_valeur'   => ['nullable', 'array'],
            'alertes_intervalle_valeur.*' => ['integer', 'min:1'],
            'alertes_intervalle_unite'    => ['nullable', 'array'],
            'alertes_intervalle_unite.*'  => ['in:secondes,minutes,heures,jours'],
        ]);

        $rappelsAvant = [];
        foreach ($request->rappels_avant_valeur ?? [] as $i => $valeur) {
            $rappelsAvant[] = [
                'valeur' => (int) $valeur,
                'unite'  => $request->rappels_avant_unite[$i] ?? 'heures',
            ];
        }

        $alertesApres = [];
        foreach ($request->alertes_apres_valeur ?? [] as $i => $valeur) {
            $alertesApres[] = [
                'valeur'            => (int) $valeur,
                'unite'             => $request->alertes_apres_unite[$i] ?? 'heures',
                'intervalle_valeur' => (int) ($request->alertes_intervalle_valeur[$i] ?? 1),
                'intervalle_unite'  => $request->alertes_intervalle_unite[$i] ?? 'heures',
            ];
        }

        $activite->update([
            'titre'          => $request->titre,
            'description'    => $request->description,
            'date'           => $request->date,
            'lieu'           => $request->lieu,
            'rappels_avant'  => $rappelsAvant,
            'alertes_apres'  => $alertesApres,
        ]);

        $acces = $activite->acces()->updateOrCreate(
            ['activite_id' => $activite->id],
            ['droitacces'  => $request->droitacces]
        );

        if ($request->droitacces === 'restreint') {
            $acces->membres()->sync($request->membres ?? []);
        } else {
            $acces->membres()->sync([]);
        }

        // Supprime toutes les notifications programmées non encore envoyées
        // (rappels, alertes ET l'ancienne notification "debut"), pour les
        // reconstruire avec la date éventuellement mise à jour.
        $activite->notifications()->where('envoye', false)->delete();

        $dateEvenement = \Carbon\Carbon::parse($request->date);

        foreach ($rappelsAvant as $r) {
            $activite->notifications()->create([
                'type'              => 'rappel',
                'dateenvoi'         => $this->calculerDate($dateEvenement, $r['valeur'], $r['unite'], 'avant'),
                'envoye'            => false,
                'repetition_active' => false,
            ]);
        }

        foreach ($alertesApres as $a) {
            $activite->notifications()->create([
                'type'               => 'alerte',
                'dateenvoi'          => $this->calculerDate($dateEvenement, $a['valeur'], $a['unite'], 'apres'),
                'envoye'             => false,
                'intervalle_valeur'  => $a['intervalle_valeur'],
                'intervalle_unite'   => $a['intervalle_unite'],
                'repetition_active'  => true,
            ]);
        }

        // Notification "début", reprogrammée sur la nouvelle date de l'activité.
        $activite->notifications()->create([
            'type'              => 'debut',
            'dateenvoi'         => $dateEvenement,
            'envoye'            => false,
            'repetition_active' => false,
        ]);

        return redirect()->route($this->routePrefix() . '.index')
                         ->with('success', 'Activité mise à jour.');
    }

    public function destroy(Activite $activite)
    {
        abort_if(
            $activite->user_id !== auth()->id() && auth()->user()->role !== 'administrateur',
            403
        );
        $activite->delete();
        return back()->with('success', 'Activité supprimée.');
    }

    public function notifications(Request $request)
    {
        $userId = auth()->id();

        $query = Notification::with(['activite.user', 'activite.acces'])
            ->where('envoye', true)
            ->whereHas('activite', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                ->orWhereHas('acces', function($q2) {
                    $q2->where('droitacces', 'public');
                });
            });

        if ($request->filled('search')) {
            $query->whereHas('activite', function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('dateenvoi', '>=', $request->date_debut);
        }

        if ($request->filled('lu')) {
            $query->where('vue', $request->lu === 'oui');
        }

        $notifications = $query->latest('dateenvoi')->paginate(25)->withQueryString();

        $nonLues = Notification::where('envoye', true)
            ->where('vue', false)
            ->whereHas('activite', function($q) use ($userId) {
                $q->where('user_id', $userId)
                ->orWhereHas('acces', function($q2) {
                    $q2->where('droitacces', 'public');
                });
            })->count();

        $alertesGmail = \App\Models\AlerteReseau::where('user_id', $userId)->where('type', 'gmail')->latest('detecte_a')->paginate(25)->withQueryString();
        $alertesWhatsapp = \App\Models\AlerteReseau::where('user_id', $userId)->where('type', 'whatsapp')->latest('detecte_a')->paginate(25)->withQueryString();
        $alertesMessenger = \App\Models\AlerteReseau::where('user_id', $userId)->where('type', 'messenger')->latest('detecte_a')->paginate(25)->withQueryString();
        $alertesFacebook = \App\Models\AlerteReseau::where('user_id', $userId)->where('type', 'facebook')->latest('detecte_a')->paginate(25)->withQueryString();

        return view('responsable.notifications', compact(
            'notifications', 'nonLues', 'alertesGmail', 'alertesWhatsapp', 'alertesMessenger', 'alertesFacebook'
        ));
    }

    public function marquerLue(Notification $notification)
    {
        $activite = $notification->activite;

        // Seul le propriétaire de l'activité, ou tout le monde si elle est publique
        // (cohérent avec la liste affichée dans notifications()).
        abort_if(
            ! $activite
            || ($activite->user_id !== auth()->id() && $activite->acces?->droitacces !== 'public'),
            403
        );

        $notification->update(['vue' => true]);
        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function marquerToutesLues()
    {
        $userId = auth()->id();
        Notification::whereHas('activite', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('vue', false)->update(['vue' => true]);

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    /**
     * Bouton "ne plus rappeler" : stoppe définitivement la répétition du rappel.
     * La notification elle-même reste inchangée (déjà envoyée), seule la
     * répétition future est désactivée — aucune nouvelle notification ne sera créée.
     */
    public function arreterRappel(Notification $notification)
    {
        $activite = $notification->activite;

        abort_if(
            $activite->user_id !== auth()->id() && $activite->acces?->droitacces !== 'public',
            403
        );

        $notification->update(['repetition_active' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * Bouton "rappeler" : programme un nouveau rappel dans 5 minutes,
     * tant que cette prochaine échéance reste avant le début de l'activité.
     * Une nouvelle ligne Notification est créée (même logique que pour les
     * alertes dans EnvoyerNotifications), la ligne cliquée reste "envoye = true".
     */
    public function relancerRappel(Notification $notification)
    {
        $activite = $notification->activite;

        abort_if(
            $activite->user_id !== auth()->id() && $activite->acces?->droitacces !== 'public',
            403
        );

        if (now()->gte($activite->date)) {
            return response()->json(['success' => false, 'message' => 'Activité déjà démarrée'], 422);
        }

        $prochaineDate = now()->addMinutes(5);

        if ($prochaineDate->gte($activite->date)) {
            return response()->json(['success' => false, 'message' => "Trop proche du début de l'activité"], 422);
        }

        Notification::create([
            'activite_id'        => $activite->id,
            'type'               => 'rappel',
            'dateenvoi'          => $prochaineDate,
            'envoye'             => false,
            'intervalle_valeur'  => 5,
            'intervalle_unite'   => 'minutes',
            'repetition_active'  => true,
        ]);

        return response()->json(['success' => true]);
    }

    // Retourne le préfixe de route selon le rôle
    protected function routePrefix(): string
    {
        return match (auth()->user()->role) {
            'administrateur' => 'admin.activites',
            'responsable'    => 'responsable',
            default          => 'activites',
        };
    }

    protected function calculerDate(\Carbon\Carbon $base, int $valeur, string $unite, string $sens): \Carbon\Carbon
    {
        return match($unite) {
            'secondes' => $sens === 'avant' ? $base->copy()->subSeconds($valeur) : $base->copy()->addSeconds($valeur),
            'minutes'  => $sens === 'avant' ? $base->copy()->subMinutes($valeur) : $base->copy()->addMinutes($valeur),
            'heures'   => $sens === 'avant' ? $base->copy()->subHours($valeur)   : $base->copy()->addHours($valeur),
            'jours'    => $sens === 'avant' ? $base->copy()->subDays($valeur)    : $base->copy()->addDays($valeur),
            default    => $base->copy(),
        };
    }

    public function supprimerNotifications(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
            'type'  => ['required', 'in:activites,gmail,whatsapp,messenger,facebook'],
        ]);

        $userId = auth()->id();

        if ($request->type === 'activites') {
            Notification::whereIn('id', $request->ids)
                ->whereHas('activite', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhereHas('acces', function ($q2) {
                          $q2->where('droitacces', 'public');
                      });
                })->delete();
        } else {
            \App\Models\AlerteReseau::whereIn('id', $request->ids)
                ->where('user_id', $userId)
                ->where('type', $request->type)
                ->delete();
        }

        return back()->with('success', 'Notifications supprimées.');
    }
}