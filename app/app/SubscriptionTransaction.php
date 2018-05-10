<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTransaction extends Model
{
    protected $table = 'subs_transaction';
    
    protected $fillable = [
        'date', 'store_number', 'subs_end', 'price', 'payment_method', 'last_order_id', 'last_payment_status'
    ];

    function owner() {
        return $this->belongsTo('App\Owner');
    }

    function plan() {
        return $this->belongsTo('App\SubscriptionPlan', 'subs_plan_id');
    }
}
