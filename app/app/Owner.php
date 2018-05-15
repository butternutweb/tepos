<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /**
     * return now active subscription
     * 
     * @return App\SubscriptionTransaction
     */
    function nowActiveSubscription(){
        foreach ($this->transactions as $transaction){
            if (Carbon::now()->gt(Carbon::parse($transaction->date)) && Carbon::parse($transaction->subs_end)->gt(Carbon::now())) {
                return $transaction;
            };
        };
        return null;
    }
    
    /**
     * get the subscription with the last subscription end
     * 
     * @return App\SubscriptionTransaction
     */
    function activeSubscriptions(){
        return $this->transactions->sortBy('subs_end')->where('subs_end','>=',Carbon::now());
    }

    /**
     * calculate total transaction made in owner's stores
     * 
     * @return Number
     */
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
