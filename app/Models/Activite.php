<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $fillable = [
        'user_id', 'titre', 'description',
        'date', 'lieu',
        'rappels_avant', 'alertes_apres',
    ];

    protected $casts = [
        'date'           => 'datetime',
        'rappels_avant'  => 'array',
        'alertes_apres'  => 'array',
    ];

    /**
     * Statut calculé dynamiquement : plus de colonne 'statut' en base.
     * 'en_attente' si l'activité est à venir, 'passee' si sa date est dépassée.
     */
    public function getStatutAttribute(): string
    {
        return now()->lt($this->date) ? 'en_attente' : 'passee';
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acces()
    {
        return $this->hasOne(AccesActivite::class);
    }
}