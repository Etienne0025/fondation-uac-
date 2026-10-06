<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tdr extends Model
{
    protected $fillable = [
        'projet_id', 'user_id', 'superieur_id', 'titre', 'description',
        'fichier_nom', 'fichier_chemin', 'fichier_type',
        'soumis_par_id', 'fichier_soumis_a',
    ];

    protected $casts = [
        'fichier_soumis_a' => 'datetime',
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function user() // créateur / staff qui a publié le thème
    {
        return $this->belongsTo(User::class);
    }

    public function soumisPar()
    {
        return $this->belongsTo(User::class, 'soumis_par_id');
    }

    public function superieur()
    {
        return $this->belongsTo(User::class, 'superieur_id');
    }

    public function approbations()
    {
        return $this->hasMany(TdrApprobation::class)->with('user')->latest();
    }

    public function estApprouvePar(User $user): bool
    {
        return $this->approbations->contains('user_id', $user->id);
    }

    public function superieurAApprouve(): bool
    {
        if (!$this->superieur_id) {
            return true; // pas de supérieur désigné = pas de blocage
        }

        return $this->approbations->contains('user_id', $this->superieur_id);
    }

    public function peutEtreApprouvePar(User $user): bool
    {
        // Le supérieur désigné pour CE TDR peut toujours approuver, même sans la permission globale
        if ($this->superieur_id === $user->id) {
            return true;
        }

        // Les autres membres habilités ne peuvent approuver qu'une fois le supérieur passé
        return $user->peut_approuver_tdr && $this->superieurAApprouve();
    }

    public function nombreApprobations(): int
    {
        return $this->approbations->count();
    }

    public function avis()
    {
        return $this->hasMany(TdrAvis::class)->latest();
    }
}