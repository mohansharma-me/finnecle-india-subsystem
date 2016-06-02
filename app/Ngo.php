<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ngo extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function ngo_group() {
        return $this->belongsTo('\App\NgoGroup');
    }

    public function donations() {
        return $this->hasMany('\App\Donation');
    }

    public function donation_amount($draw_id) {

        return DB::table('transactions')
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->where('donations.ngo_id', $this->id)
            ->sum('donations.amount');

    }
}
