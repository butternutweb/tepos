<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'store';

    protected $fillable = [
        'name',
    ];

    function owner() {
        return $this->belongsTo('App\Owner');
    }

    function staffs() {
        return $this->hasMany('App\Staff');
    }

    function products() {
        return $this->belongsToMany('App\Product', 'store_product')->withPivot('id', 'selling_price');
    }
}
