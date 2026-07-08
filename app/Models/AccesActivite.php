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
}