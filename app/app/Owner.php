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

    function stores() {
        return $this->hasMany('App\Store', 'owner_id')->with('staffs');
    }

    function transactionSum(){
        $sum=0;
        foreach($this->stores as $store){
            foreach($store->staffs as $staff){
                $transactions= $staff->transactions;
                $sum += $transactions->count();
                foreach ($transactions as $transaction){
                    
                };
            };
        };
        return $sum;
    }
}
