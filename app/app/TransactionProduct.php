<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    protected $table = 'transaction_product';
    
    protected $fillable = [
        'qty', 'note'
    ];

    function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    function product() {
        return $this->belongsTo('App\Product');
    }
}
