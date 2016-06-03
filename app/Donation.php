<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function transaction() {
        return $this->belongsTo('\App\Transaction');
    }

    public function ngo() {
        return $this->belongsTo('\App\Ngo');
    }

    

    public function won() {

        $declaration_id = $this->transaction->declaration_id;
        if($declaration_id > 0) {
            $declaration = Declaration::find($declaration_id);
            if($declaration->exists()) {
                $sel_ngo = $declaration->ngo;
                $donation_ngo = $this->ngo;

                $winning_flag = false;
                // validate ngo winning by game id...
                switch ($donation_ngo->ngo_group->game->id) {
                    case 1: // single figure
                        $last_digit = $sel_ngo->ngo_total();
                        $last_digit = intval(substr($last_digit,-1));
                        if($last_digit == intval($donation_ngo->ngo)) {
                            $winning_flag=true;
                        }
                        break;
                    case 2: // jodi figure

                        break;
                    case 3: // single page
                    case 4: // double page
                    case 5: // tayo page
                        $winning_flag = $sel_ngo->id == $donation_ngo->id;
                        break;
                }

                return array($winning_flag, $winning_flag ? $this->lucky_ratio * $this->amount : 0);

            }
        }

        return array(false, 0);
    }
}
