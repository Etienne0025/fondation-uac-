<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdrAvis extends Model
{
    protected $table = 'tdr_avis';

    protected $fillable = ['tdr_id', 'user_id', 'message'];

    public function tdr()
    {
        return $this->belongsTo(Tdr::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}