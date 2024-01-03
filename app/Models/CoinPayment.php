<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'setting_coin_id',
        'transaction_id',
        'unit',
        'price',
        'amount',
        'type',
        'status',
        'remarks'
    ];
}
