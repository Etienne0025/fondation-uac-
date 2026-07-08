<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlerteReseau extends Model
{
    protected $table = 'alertes_reseau';

    protected $fillable = [
        'user_id',
        'reseau_social_id',
        'type',
        'expediteur',
        'sujet',
        'messages_non_lus',
        'vue',
        'detecte_a',
    ];

    protected $casts = [
        'detecte_a' => 'datetime',
        'vue'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reseauSocial()
    {
        return $this->belongsTo(ReseauSocial::class, 'reseau_social_id');
    }
}