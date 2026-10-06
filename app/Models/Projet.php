<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = ['titre', 'description', 'user_id','superieur_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tdrs()
    {
        return $this->hasMany(Tdr::class);
    }
    
}