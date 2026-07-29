<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccesActivite extends Model
{
    protected $table = 'acces_activites';

    protected $fillable = ['activite_id', 'droitacces'];

    public function activite()
    {
        return $this->belongsTo(Activite::class);
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'acces_activite_utilisateur', 'acces_activite_id', 'user_id')
                     ->withTimestamps();
    }
}