<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversionRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'usd',
        'currency',
        'price',
    ];
}
