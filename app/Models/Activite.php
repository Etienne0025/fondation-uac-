<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $fillable = [
        'user_id', 'titre', 'description',
        'date', 'lieu', 'statut',
        'rappels_avant', 'alertes_apres', // ← manquait ici
    ];

    protected $casts = [
        'date'           => 'datetime',
        'rappels_avant'  => 'array',
        'alertes_apres'  => 'array',
    ];

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