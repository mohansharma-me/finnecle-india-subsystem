<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralSetting extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public static function settings() {
    	return GeneralSetting::find(1);
    }

}
