<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    
    protected $fillable = [
        'name'
    ];

    function subCategories() {
        return $this->hasMany('App\SubCategory', 'category_id');
    }

    function owner() {
        return $this->belongsTo('App\Owner');
    }
}
