<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NgoGroup extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function game() {
        return $this->belongsTo('\App\Game');
    }

    public function ngos() {
        return $this->hasMany('\App\Ngo');
    }

}
