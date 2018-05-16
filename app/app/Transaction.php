<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    
    protected $fillable = [
        'date', 'note'
    ];

    function staff() {
        return $this->belongsTo('App\Staff');
    }

    function status() {
        return $this->belongsTo('App\Status');
    }

    function products() {
        return $this->belongsToMany('App\Product', 'transaction_product')->withPivot('id', 'qty', 'note');
    }

    /**
     * return a transaction total amount price and product quantity
     * 
     * @return Array
     */
    function amount(){
        $amount=0;
        $quant=0;
        foreach ($this->products as $product){
            $quant += $product->pivot->qty;
            $amount += $product->pivot->qty * $product->stores->find($this->staff->store->id)->pivot->selling_price;
        };
        return ['value'=>$amount,'qty'=>$quant];
    }

}
