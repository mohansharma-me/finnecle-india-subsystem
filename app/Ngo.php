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

    public function return_amount($draw_id) {

        $first = DB::table('transactions')
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->where('donations.ngo_id', $this->id)
            //->select()
            ->sum(DB::raw('donations.amount*donations.lucky_ratio'));

        return $first;

    }

    public function return_commission_amount($draw_id) {

        $rows = DB::table('transactions')
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->where('donations.ngo_id', $this->id)
            //->select()
            ->groupBy('transactions.id')
            ->select(DB::raw('transactions.*, (donations.amount*transactions.center_commission/100) as tran_comm_amount'))
            ->get();


        $totalComm = 0;
        foreach($rows as $row) {
            $totalComm += $row->tran_comm_amount;
        }

        return $totalComm;

    }

    public function ngo_total() {

        $ngo_number = $this->ngo;
        $value = 0;
        for($i=0;$i<strlen($ngo_number);$i++) {
            $value += intval($ngo_number[$i]);
        }
        return $value;

    }
}
