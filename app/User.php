<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    protected $hidden = ['password','remember_token','deleted_at','modified_at','created_at','updated_at'];

    public function hasRole($role) {
        return strtolower(trim($this->role)) == strtolower(trim($role));
    }

    public function getRole($append=false) {
        if($append) {
            return $this->role.".".$append;
        }
        return $this->role;
    }

    public function isCenter() {

        $center = Center::where('user_id', $this->id)->first();
        if($center) {
            return $center;
        }
        return false;

    }

    public function center() {
        return $this->belongsTo('\App\Center', 'id', 'user_id');
    }

    public function getUnclearAmount() {

        $center = $this->center;

        switch($this->role) {
            case "donator":

                $sum = 0.00;
                $com_sum = 0.00;
                $ids = [];
                $transactions = $center->transactions()->where('clear_request_id', 0)->get();
                foreach($transactions as $transaction) {
                    $tr_amount = $transaction->amount();
                    //$tr_amount = $transaction->lucky_amount();
                    $tr_com = $tr_amount * $transaction->center_commission / 100;
                    $sum += $tr_amount - $tr_com;
                    $com_sum += $tr_com;
                    $ids[] = $transaction->id;
                }

                return [$sum, $ids, $com_sum];

                break;
            case "cashier":

                $sum = 0.00;
                $com_sum = 0.00;
                $ids = [];
                $paid_transactions = $center->paid_transactions()->where('clear_request_id', 0)->get();

                foreach($paid_transactions as $paid_transaction) {
                    $transaction = $paid_transaction->transaction;
                    //$tr_amount = $transaction->amount();
                    $tr_amount = $transaction->lucky_amount();
                    $tr_com = $tr_amount * $paid_transaction->center_commission / 100;
                    $sum += $tr_amount - $tr_com;
                    $com_sum += $tr_com;
                    $ids[] = $paid_transaction->id;
                }

                return [$sum, $ids, $com_sum];


                break;
        }

        return array(0, [], 0);

    }
}
