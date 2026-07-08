<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReseauSocial extends Model
{
    protected $table = 'reseau_sociaux';

    protected $fillable = [
    'user_id', 'nomcompte', 'email_compte',
    'telephone_compte', 'access_token', 'refresh_token',
    'delai_alerte', 'delai_unite', 'actif', 'derniere_verification',
];

    protected $hidden = ['access_token', 'refresh_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}