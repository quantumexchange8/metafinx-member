<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinMarketTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'setting_coin_id',
        'updated_by',
        'open_time',
        'close_time',
        'frequency_type',
        'frequency',
    ];

    protected $casts = [
        'frequency' => 'array',
    ];
}
