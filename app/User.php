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
}
