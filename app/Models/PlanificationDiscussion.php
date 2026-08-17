<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanificationDiscussion extends Model
{
    protected $table = 'planification_discussions';

    protected $fillable = [
        'membre_id',
        'user_id',
        'semaine_debut',
        'message',
    ];

    protected $casts = [
        'semaine_debut' => 'date',
    ];

    public function membre()
    {
        return $this->belongsTo(User::class, 'membre_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}