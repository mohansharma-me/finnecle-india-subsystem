<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ngo extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function ngo_group() {
        return $this->belongsTo('\App\NgoGroup');
    }
}
