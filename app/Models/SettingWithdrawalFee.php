<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingWithdrawalFee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'updated_by',
    ];
}
