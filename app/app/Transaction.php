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
}
