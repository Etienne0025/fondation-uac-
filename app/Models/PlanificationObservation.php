<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificationObservation extends Model
{
    protected $table = 'planification_observations';

    protected $fillable = [
        'user_id',
        'semaine_debut',
        'observation_admin',
        'observation_personnelle',
    ];

    protected $casts = [
        'semaine_debut' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}