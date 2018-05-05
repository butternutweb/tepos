<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    
    protected $table = 'staff';

    protected $fillable = [
        'salary',
    ];

    function accounts() {
        return $this->morphMany('App\Account', 'child');
    }

    function store() {
        return $this->belongsTo('App\Store');
    }
}
