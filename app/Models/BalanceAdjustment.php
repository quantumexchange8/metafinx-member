<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'to_user_id',
        'to_wallet_id',
        'type',
        'old_balance',
        'amount',
        'new_balance',
        'description',
    ];
}
