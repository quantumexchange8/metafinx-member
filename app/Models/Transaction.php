<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category',
        'transaction_type',
        'from_wallet_id',
        'to_wallet_id',
        'from_coin_id',
        'to_coin_id',
        'transaction_number',
        'to_wallet_address',
        'txn_hash',
        'amount',
        'transaction_charges',
        'transaction_amount',
        'unit',
        'price_per_unit',
        'status',
        'remarks',
    ];
}
