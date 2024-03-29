<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinPrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'setting_coin_id',
        'updated_by',
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

    public function getPriceDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d'); // Change 'Y-m-d' to your desired format
    }

}
