<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'owner';

    protected $fillable = [
    ];

    function accounts() {
        return $this->morphMany('App\Account', 'child');
    }

    function transactions() {
        return $this->hasMany('App\SubscriptionTransaction', 'owner_id');
    }

    function staffs() {
        return $this->hasMany('App\Staff', 'owner_id');
    }
}
