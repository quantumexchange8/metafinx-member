<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingCountry extends Model
{
    use HasFactory;

    protected $table = 'setting_countries';

    public static function get_country_sel(string $language): array
    {
        $query = SettingCountry::query();

        switch ($language) {
            case 'cn':
                $column = 'name_cn';
                break;
            case 'tw':
                $column = 'name_tw';
                break;
            default:
                $column = 'name';
        }

        return $query->orderBy('id')->get()->pluck($column, 'name')->toArray();
    }
}
