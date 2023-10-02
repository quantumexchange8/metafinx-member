<?php

namespace App\Services;

use App\Models\RunningNumber;
use Illuminate\Support\Str;

class RunningNumberService
{
    public static function getID($type)
    {
        $format = RunningNumber::where('type', $type)->first();
        $lastID =  $format['last_number'] + 1;
        $format->increment('last_number');
        $format->save();
        return $format['prefix'] . Str::padLeft($lastID, $format['digits'], "0");
    }
}
