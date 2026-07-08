<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
   protected $fillable = [
    'activite_id', 'type', 'dateenvoi', 'envoye',
    'intervalle_valeur', 'intervalle_unite', 'repetition_active', 'vue',
];

protected $casts = [
    'dateenvoi'          => 'datetime',
    'envoye'             => 'boolean',
    'repetition_active'  => 'boolean',
    'vue'                => 'boolean',
];

    public function activite()
    {
        return $this->belongsTo(Activite::class);
    }
}