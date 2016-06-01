<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuckyRatio extends Model
{
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    protected $fillable = ['ratio', 'game_id'];

    public function center() {
        return $this->belongsTo('\App\Center');
    }

    public function game() {
        return $this->belongsTo('\App\Game');
    }
}
