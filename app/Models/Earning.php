<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'upline_id',
        'upline_rank_id',
        'upline_wallet_id',
        'downline_id',
        'downline_rank_id',
        'before_amount',
        'percentage',
        'after_amount',
        'type',
    ];
}
