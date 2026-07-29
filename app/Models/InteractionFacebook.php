<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionFacebook extends Model
{
    protected $table = 'interactions_facebook';

    protected $fillable = [
        'reseau_social_id', 'fb_object_id', 'type',
        'expediteur', 'contenu', 'recu_a', 'repondu_a', 'alerte_envoyee_a',
    ];

    protected $casts = [
        'recu_a' => 'datetime',
        'repondu_a' => 'datetime',
        'alerte_envoyee_a' => 'datetime',
    ];

    public function reseau()
    {
        return $this->belongsTo(ReseauSocial::class, 'reseau_social_id');
    }
}