<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    public function ngo_groups()  {
        return $this->hasMany('\App\NgoGroup');
    }

}
