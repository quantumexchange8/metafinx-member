<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingCoin extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'symbol',
        'price',
        'price_date',
        'open_time',
        'close_time',
        'duration',
    ];

    protected $casts = [
        'price_date' => 'timestamp',
        'duration' => 'array',
    ];
}
