<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Draw extends Model
{
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    public function channel() {
        return $this->belongsTo('\App\Channel');
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::deleting(function($draw) {

        });
    }
}
