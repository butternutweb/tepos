<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_category';
    
    protected $fillable = [
        'name'
    ];

    function category() {
        return $this->belongsTo('App\Category');
    }

    function products() {
        return $this->hasMany('App\Product');
    }
}
