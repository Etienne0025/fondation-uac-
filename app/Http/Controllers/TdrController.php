<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Tdr;
use App\Models\TdrAvis;
use App\Models\User;
use App\Notifications\TdrPublieNotification;
use App\Notifications\TdrAvisNotification;
use App\Notifications\TdrApprouveNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TdrPublieMail;
use App\Mail\TdrValidationDemandeeMail;

class TdrController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['administrateur', 'responsable']), 403);

        $query = Tdr::with(['projet', 'user', 'approbations', 'avis']);

        if ($request->filled('q')) {
            $query->where('titre', 'like', '%' . $request->q . '%');
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
        abort_unless(in_array(auth()->user()->role, ['administrateur', 'responsable']), 403);

        $projets = Projet::where('user_id', auth()->id())->orWhereDoesntHave('tdrs')->get();

        // Seuls administrateur et responsable peuvent être désignés supérieur hiérarchique
        $utilisateurs = User::whereIn('role', ['administrateur', 'responsable'])
            ->where('id', '!=', auth()->id())
            ->orderBy('nom')
            ->get();

        return view('tdr.create', compact('projets', 'utilisateurs'));
    }

    // Seuls administrateur et responsable publient le theme, avec ou sans fichier joint immediatement
    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['administrateur', 'responsable']), 403);

        $request->validate([
            'projet_id'      => ['nullable', 'exists:projets,id'],
            'nouveau_projet' => ['required_without:projet_id', 'string', 'max:255'],
            'titre'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'fichier'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'superieur_id'   => ['required', 'exists:users,id', 'different:' . auth()->id()],
        ]);

        $projet = $request->projet_id
            ? Projet::findOrFail($request->projet_id)
            : Projet::create([
                'titre'   => $request->nouveau_projet,
                'user_id' => auth()->id(),
            ]);

        $donneesTdr = [
            'projet_id'    => $projet->id,
            'user_id'      => auth()->id(),
            'superieur_id' => $request->superieur_id,
            'titre'        => $request->titre,
            'description'  => $request->description,
        ];

        if ($request->hasFile('fichier')) {
            $fichier = $request->file('fichier');
            $donneesTdr['fichier_nom']      = $fichier->getClientOriginalName();
            $donneesTdr['fichier_chemin']   = $fichier->store('tdrs', 'public');
            $donneesTdr['fichier_type']     = $fichier->getClientOriginalExtension();
            $donneesTdr['soumis_par_id']    = auth()->id();
            $donneesTdr['fichier_soumis_a'] = now();
        }

        $tdr = Tdr::create($donneesTdr);

        // Notification push a tout le staff (administrateur + responsable), hors createur
        $staff = User::whereIn('role', ['administrateur', 'responsable'])
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($staff as $membre) {
            $membre->notify(new TdrPublieNotification($tdr));
        }

        // Email a tout le staff, et email specifique aux personnes appelees a valider
        // (le superieur designe + les membres ayant la permission globale, sans doublon)
        try {
            $tousLesStaff = User::whereIn('role', ['administrateur', 'responsable'])->get();
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
        } catch (\Exception $e) {
            \Log::error("Échec d'envoi des emails de publication TDR #{$tdr->id} : " . $e->getMessage());
        }

        return redirect()->route('tdr.show', $tdr)->with('success', 'TDR publié avec succès.');
    }

    public function show(Tdr $tdr)
    {
        abort_unless(in_array(auth()->user()->role, ['administrateur', 'responsable']), 403);

        $tdr->load(['projet', 'user', 'soumisPar', 'superieur', 'avis.user', 'approbations.user']);

        return view('tdr.show', compact('tdr'));
    }

    // Ajouter ou remplacer le fichier apres la publication initiale
    public function soumettreFichier(Request $request, Tdr $tdr)
    {
        abort_unless($tdr->user_id === auth()->id(), 403, 'Seul le créateur de ce TDR peut modifier son fichier.');

        $request->validate([
            'fichier' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if ($tdr->fichier_chemin) {
            Storage::disk('public')->delete($tdr->fichier_chemin);
        }

        $fichier = $request->file('fichier');
        $chemin  = $fichier->store('tdrs', 'public');

        $tdr->update([
            'fichier_nom'      => $fichier->getClientOriginalName(),
            'fichier_chemin'   => $chemin,
            'fichier_type'     => $fichier->getClientOriginalExtension(),
            'soumis_par_id'    => auth()->id(),
            'fichier_soumis_a' => now(),
        ]);

        return back()->with('success', 'Fichier soumis avec succès.');
    }

    public function storeAvis(Request $request, Tdr $tdr)
    {
        abort_unless(in_array(auth()->user()->role, ['administrateur', 'responsable']), 403);

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
        // Le superieur designe peut approuver meme sans la permission globale peut_approuver_tdr,
        // et les autres membres habilites ne peuvent le faire qu'une fois le superieur passe.
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

        $utilisateurs = User::whereIn('role', ['administrateur', 'responsable'])
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
            'superieur_id' => ['required', 'exists:users,id', 'different:' . auth()->id()],
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

        if ($tdr->fichier_chemin) {
            Storage::disk('public')->delete($tdr->fichier_chemin);
        }

        $tdr->delete();

        return redirect()->route('tdr.index')->with('success', 'TDR supprimé.');
    }
}