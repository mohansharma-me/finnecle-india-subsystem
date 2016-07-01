<?php

namespace App;

use App\Donation;
use App\Ngo;
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

    public function donation_amount($draw_id, $created_at = null) {
        if(!isset($created_at)) {
            $created_at = \Carbon\Carbon::now();
        }
        return DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->where('donations.ngo_id', $this->id)
            ->sum('donations.amount');

    }

    public static function getNgoId($ngoNumber) {

        $ngo = Ngo::where('ngo', $ngoNumber)->first();
        if($ngo) {
            return $ngo->id;
        }

        return 0;
    }

    public static function total_donator_commission($draw_id, $created_at = null) {
        if(!isset($created_at)) {
            $created_at = \Carbon\Carbon::now();
        }

        $totalCommission = DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            //->whereIn('donations.ngo_id', $ngoIds)
            //->select()
            ->sum(DB::raw('(donations.amount*transactions.center_commission/100)'));

        return $totalCommission;
    }

    public function slip_count($draw_id, $created_at = null) {
        if(!isset($created_at)) {
            $created_at = \Carbon\Carbon::now();
        }
        $ngoIds = [ $this->id ];
        
        if($this->ngo_group && $this->ngo_group->game && $this->ngo_group->game->id > 2) {
            $ngoIds[] = self::getNgoId($this->last_digit());
        }

        $totalCommission = DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->whereIn('donations.ngo_id', $ngoIds)
            //->select()
            
            ->count(DB::raw('transactions.id'));

        return $totalCommission;
    }

    public function return_amount($draw_id, $created_at = null) {
        if(!isset($created_at)) {
            $created_at = \Carbon\Carbon::now();
        }

        $cashier_comm = GeneralSetting::settings()->cashier_commission_ratio;

        $ngoIds = [ $this->id ];
        
        if($this->ngo_group && $this->ngo_group->game && $this->ngo_group->game->id > 2) {
            $ngoIds[] = self::getNgoId($this->last_digit());
        }

        $totalCommission = DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            //->whereIn('donations.ngo_id', $ngoIds)
            //->select()
            ->sum(DB::raw('(donations.amount*transactions.center_commission/100)'));

        $first = DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->whereIn('donations.ngo_id', $ngoIds)
            //->select()
            ->sum(DB::raw('(donations.amount*donations.lucky_ratio)+((donations.amount*donations.lucky_ratio)*'.$cashier_comm.'/100)'));

        return $first + $totalCommission;

    }

    public function return_commission_amount($draw_id, $created_at = null) {
        if(!isset($created_at)) {
            $created_at = \Carbon\Carbon::now();
        }

        $ngoIds = [ $this->id ];
        
        if($this->ngo_group && $this->ngo_group->game && $this->ngo_group->game->id > 2) {
            $ngoIds[] = self::getNgoId($this->last_digit());
        }

        $rows = DB::table('transactions')
            ->whereDate('transactions.created_at', '=', $created_at->format('Y-m-d'))
            ->where('transactions.declaration_id',0)
            ->where('transactions.draw_id', $draw_id)
            ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
            ->whereIn('donations.ngo_id', $ngoIds)
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

    public function last_digit() {
        return intval(substr($this->ngo_total(),-1));
    }
}
