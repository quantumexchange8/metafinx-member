<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'coin_id',
        'setting_coin_id',
        'type',
        'old_unit',
        'unit',
        'new_unit',
        'description',
        'handle_by',
    ];
}
