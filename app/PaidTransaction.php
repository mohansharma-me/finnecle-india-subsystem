<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaidTransaction extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function transaction() {
        return $this->belongsTo('\App\Transaction');
    }

    public function center() {
        return $this->belongsTo('\App\Center');
    }
}
