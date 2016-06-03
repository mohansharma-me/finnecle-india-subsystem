<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Declaration extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function transactions() {
        return $this->hasMany('\App\Transaction');
    }

    public function ngo() {
        return $this->belongsTo('\App\Ngo');
    }
}
