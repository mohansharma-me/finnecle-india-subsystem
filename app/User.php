<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    public function hasRole($role) {
        return strtolower(trim($this->role)) == strtolower(trim($role));
    }

    public function getRole($append=false) {
        if($append) {
            return $this->role.".".$append;
        }
        return $this->role;
    }
}
