<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    
    protected $fillable = [
        'name', 'sku', 'note', 'capital_price'
    ];

    function subCategory() {
        return $this->belongsTo('App\SubCategory');
    }

    function transactions() {
        return $this->belongsToMany('App\Transaction', 'transaction_product', 'product_id', 'transaction_id')->withPivot('quantity', 'note')->withTimestamps();
    }

    function stores() {
        return $this->belongsToMany('App\Store', 'store_product', 'product_id', 'store_id')->withPivot('selling_price')->withTimestamps();
    }
}
