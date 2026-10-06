<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdrApprobation extends Model
{
    protected $table = 'tdr_approbations';

    protected $fillable = ['tdr_id', 'user_id'];

    public function tdr()
    {
        return $this->belongsTo(Tdr::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}