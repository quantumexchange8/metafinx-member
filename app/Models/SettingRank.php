<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingRank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'self_deposit',
        'valid_direct_referral',
        'valid_affiliate_deposit',
        'capping_per_line',
    ];
}
