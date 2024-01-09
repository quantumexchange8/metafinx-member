<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'coin_id',
        'setting_coin_id',
        'type',
        'old_amount',
        'amount',
        'new_amount',
        'description',
        'handle_by',
    ];
}
