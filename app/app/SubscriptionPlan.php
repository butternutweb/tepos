<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table = 'subs_plan';
    
    protected $fillable = [
        'name', 'duration_day', 'price', 'store_number'
    ];

    function transactions() {
        return $this->hasMany('App\SubscriptionTransaction', 'subs_plan_id');
    }
}
