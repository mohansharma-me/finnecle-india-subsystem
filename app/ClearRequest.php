<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClearRequest extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function center() {
        return $this->belongsTo('\App\Center');
    }

    public function transactions() {
        return $this->hasMany('\App\Transaction');
    }
}
