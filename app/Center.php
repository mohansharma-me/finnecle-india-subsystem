<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Center extends Model
{
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    public function ratios() {
        return $this->hasMany('\App\LuckyRatio');
    }

    public function user() {
        return $this->belongsTo('\App\User');
    }

    public function transactions() {
        return $this->hasMany('\App\Transaction');
    }

    public function paid_transactions() {
        return $this->hasMany('\App\PaidTransaction');
    }

    public function clear_requests() {
        return $this->hasMany('\App\ClearRequest');
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::deleting(function($center) {
            $center->ratios()->delete();
            $center->user()->delete();
            $center->transactions()->delete();
            $center->paid_transactions()->delete();
        });
    }

}
