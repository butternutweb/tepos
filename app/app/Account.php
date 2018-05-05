<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'account';

    protected $fillable = [
        'username', 'email', 'password', 'name', 'phone',
    ];

    protected $hidden = [
        'password', 'remember_token', 'verification_token', 'verification_token_end', 'forgot_token', 'forgot_token_end'
    ];

    function child() {
        return $this->morphTo();
    }

    function status() {
        return $this->belongsTo('App\Status');
    }
}
