<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    protected $table = 'cost';
    
    protected $fillable = [
        'name', 'amount'
    ];

    function store() {
        return $this->belongsTo('App\Store');
    }
}
