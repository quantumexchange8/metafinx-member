<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingEarning extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'setting_rank_id',
        'name',
        'type',
        'value',
    ];
}
