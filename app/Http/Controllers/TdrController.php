<?php

namespace App\Http\Controllers;

use App\Mail\TdrPublieMail;
use App\Mail\TdrValidationDemandeeMail;
use App\Models\Projet;
use App\Models\Tdr;
use App\Models\User;
use App\Notifications\TdrApprouveNotification;
use App\Notifications\TdrAvisNotification;
use App\Notifications\TdrPublieNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TdrController extends Controller
{
    private const ROLES_STAFF   = ['administrateur', 'responsable'];
    private const DISQUE_PRIVE  = 'local'; // non accessible directement depuis le web

    public function index(Request $request)
    {
        $this->autoriserStaff();

        $request->validate([
            'q'          => ['nullable', 'string', 'max:100'],
            'user_id'    => ['nullable', 'integer'],
            'date_debut' => ['nullable', 'date'],
            'date_fin'   => ['nullable', 'date'],
        ]);

        $query = Tdr::with(['projet', 'user', 'approbations', 'avis']);

        if ($request->filled('q')) {
            // On échappe % et _ pour qu'ils soient traités comme du texte
            $recherche = addcslashes($request->q, '%_\\');
            $query->where('titre', 'like', '%' . $recherche . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $tdrs = $query->latest()->paginate(10);

        // Seulement les personnes ayant déjà publié au moins un TDR
        $publieurs = User::whereIn('id', Tdr::select('user_id'))
            ->orderBy('nom')
            ->get();

        return view('tdr.index', compact('tdrs', 'publieurs'));
    }

    public function create()
    {
        $this->autoriserStaff();

        $projets = Projet::where('user_id', auth()->id())->orWhereDoesntHave('tdrs')->get();

        // Seuls administrateur et responsable peuvent être désignés supérieur hiérarchique
        $utilisateurs = User::whereIn('role', self::ROLES_STAFF)
            ->where('id', '!=', auth()->id())
            ->orderBy('nom')
            ->get();

        return view('tdr.create', compact('projets', 'utilisateurs'));
    }

    // Seuls administrateur et responsable publient le thème, avec ou sans fichier joint
    public function store(Request $request)
    {
        $this->autoriserStaff();

        $request->validate([
            'projet_id'      => ['nullable', 'integer', 'exists:projets,id'],
            'nouveau_projet' => ['required_without:projet_id', 'nullable', 'string', 'max:255'],
            'titre'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'fichier'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'superieur_id'   => [
                'required',
                // Le supérieur doit exister ET être administrateur ou responsable
                Rule::exists('users', 'id')->whereIn('role', self::ROLES_STAFF),
                // (remplace 'different:' . id, qui attend un nom de champ et non une valeur)
                Rule::notIn([auth()->id()]),
            ],
        ]);

        if ($request->projet_id) {
            // Même règle que dans create() : projet à soi, ou projet sans TDR
            $projet = Projet::where('id', $request->projet_id)
                ->where(function ($q) {
                    $q->where('user_id', auth()->id())
                      ->orWhereDoesntHave('tdrs');
                })
                ->firstOrFail();
        } else {
            $projet = Projet::create([
                'titre'   => $request->nouveau_projet,
                'user_id' => auth()->id(),
            ]);
        }

        $donneesTdr = [
            'projet_id'    => $projet->id,
            'user_id'      => auth()->id(),
            'superieur_id' => $request->superieur_id,
            'titre'        => $request->titre,
            'description'  => $request->description,
        ];

        if ($request->hasFile('fichier')) {
            $fichier = $request->file('fichier');
            $donneesTdr['fichier_nom']      = basename($fichier->getClientOriginalName());
            $donneesTdr['fichier_chemin']   = $fichier->store('tdrs', self::DISQUE_PRIVE);
            $donneesTdr['fichier_type']     = $fichier->extension(); // déduit du contenu, pas du client
            $donneesTdr['soumis_par_id']    = auth()->id();
            $donneesTdr['fichier_soumis_a'] = now();
        }

        $tdr = Tdr::create($donneesTdr);

        $this->notifierPublication($tdr);

        return redirect()->route('tdr.show', $tdr)->with('success', 'TDR publié avec succès.');
    }

    public function show(Tdr $tdr)
    {
        $this->autoriserStaff();

        $tdr->load(['projet', 'user', 'soumisPar', 'superieur', 'avis.user', 'approbations.user']);

        return view('tdr.show', compact('tdr'));
    }

    // Téléchargement protégé du fichier (remplace l'accès direct au dossier public)
    public function telecharger(Tdr $tdr)
    {
        $this->autoriserStaff();

        abort_unless($tdr->fichier_chemin, 404);

        // 'public' = anciens fichiers déjà envoyés avant cette correction
        foreach ([self::DISQUE_PRIVE, 'public'] as $disque) {
            if (Storage::disk($disque)->exists($tdr->fichier_chemin)) {
                return Storage::disk($disque)->download(
                    $tdr->fichier_chemin,
                    basename($tdr->fichier_nom ?? 'tdr')
                );
            }
        }

        abort(404);
    }

    // Ajouter ou remplacer le fichier après la publication initiale
    public function soumettreFichier(Request $request, Tdr $tdr)
    {
        $this->autoriserStaff();
        abort_unless($tdr->user_id === auth()->id(), 403, 'Seul le créateur de ce TDR peut modifier son fichier.');
        abort_if($tdr->nombreApprobations() > 0, 403, 'Ce TDR a déjà été approuvé, son fichier ne peut plus être modifié.');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $this->supprimerFichier($tdr);

        $fichier = $request->file('fichier');
        $chemin  = $fichier->store('tdrs', self::DISQUE_PRIVE);

        $tdr->update([
            'fichier_nom'      => basename($fichier->getClientOriginalName()),
            'fichier_chemin'   => $chemin,
            'fichier_type'     => $fichier->extension(),
            'soumis_par_id'    => auth()->id(),
            'fichier_soumis_a' => now(),
        ]);

        return back()->with('success', 'Fichier soumis avec succès.');
    }

    public function storeAvis(Request $request, Tdr $tdr)
    {
        $this->autoriserStaff();

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $avis = $tdr->avis()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Notifie le créateur du TDR, sauf s'il commente son propre TDR
        if ($tdr->user_id !== auth()->id()) {
            $tdr->user->notify(new TdrAvisNotification($avis));
        }

        return back()->with('success', 'Avis envoyé.');
    }

    public function approuver(Tdr $tdr)
    {
        // Le supérieur désigné peut approuver même sans la permission globale peut_approuver_tdr,
        // et les autres membres habilités ne peuvent le faire qu'une fois le supérieur passé.
        abort_unless($tdr->peutEtreApprouvePar(auth()->user()), 403);

        if ($tdr->estApprouvePar(auth()->user())) {
            return back()->with('warning', 'Vous avez déjà approuvé ce TDR.');
        }

        $tdr->approbations()->create([
            'user_id' => auth()->id(),
        ]);

        if ($tdr->user_id !== auth()->id()) {
            $tdr->user->notify(new TdrApprouveNotification($tdr, auth()->user()));
        }

        return back()->with('success', 'TDR approuvé.');
    }

    public function edit(Tdr $tdr)
    {
        abort_unless($tdr->user_id === auth()->id(), 403, 'Seul le créateur de ce TDR peut le modifier.');
        abort_if($tdr->nombreApprobations() > 0, 403, 'Ce TDR a déjà été approuvé, il ne peut plus être modifié.');

        $utilisateurs = User::whereIn('role', self::ROLES_STAFF)
            ->where('id', '!=', auth()->id())
            ->orderBy('nom')
            ->get();

        return view('tdr.edit', compact('tdr', 'utilisateurs'));
    }

    public function update(Request $request, Tdr $tdr)
    {
        abort_unless($tdr->user_id === auth()->id(), 403, 'Seul le créateur de ce TDR peut le modifier.');
        abort_if($tdr->nombreApprobations() > 0, 403, 'Ce TDR a déjà été approuvé, il ne peut plus être modifié.');

        $request->validate([
            'titre'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'superieur_id' => [
                'required',
                Rule::exists('users', 'id')->whereIn('role', self::ROLES_STAFF),
                Rule::notIn([auth()->id()]),
            ],
        ]);

        $tdr->update([
            'titre'        => $request->titre,
            'description'  => $request->description,
            'superieur_id' => $request->superieur_id,
        ]);

        return redirect()->route('tdr.show', $tdr)->with('success', 'TDR mis à jour.');
    }

    public function destroy(Tdr $tdr)
    {
        abort_unless($tdr->user_id === auth()->id(), 403, 'Seul le créateur de ce TDR peut le supprimer.');
        abort_if($tdr->nombreApprobations() > 0, 403, 'Ce TDR a déjà été approuvé, il ne peut plus être supprimé.');

        $this->supprimerFichier($tdr);

        $tdr->delete();

        return redirect()->route('tdr.index')->with('success', 'TDR supprimé.');
    }

    /* ------------------------------------------------------------------ */
    /*  Méthodes privées                                                   */
    /* ------------------------------------------------------------------ */

    private function autoriserStaff(): void
    {
        abort_unless(in_array(auth()->user()->role, self::ROLES_STAFF, true), 403);
    }

    private function supprimerFichier(Tdr $tdr): void
    {
        if ($tdr->fichier_chemin) {
            Storage::disk(self::DISQUE_PRIVE)->delete($tdr->fichier_chemin);
            Storage::disk('public')->delete($tdr->fichier_chemin); // anciens fichiers
        }
    }

    /**
     * Notifications push + emails. Chaque bloc est isolé : une panne d'envoi
     * ne doit jamais faire échouer la publication du TDR (déjà enregistré).
     */
    private function notifierPublication(Tdr $tdr): void
    {
        try {
            $staff = User::whereIn('role', self::ROLES_STAFF)
                ->where('id', '!=', auth()->id())
                ->get();

            foreach ($staff as $membre) {
                $membre->notify(new TdrPublieNotification($tdr));
            }
        } catch (\Throwable $e) {
            Log::error("Échec des notifications push TDR #{$tdr->id} : " . $e->getMessage());
        }

        try {
            $tousLesStaff = User::whereIn('role', self::ROLES_STAFF)->get();
            foreach ($tousLesStaff as $membre) {
                Mail::to($membre->email)->send(new TdrPublieMail($tdr));
            }

            $destinatairesValidation = User::where('peut_approuver_tdr', true)
                ->orWhere('id', $tdr->superieur_id)
                ->get()
                ->unique('id');

            foreach ($destinatairesValidation as $destinataire) {
                Mail::to($destinataire->email)->send(new TdrValidationDemandeeMail($tdr));
            }
        } catch (\Throwable $e) {
            Log::error("Échec d'envoi des emails de publication TDR #{$tdr->id} : " . $e->getMessage());
        }
    }
}