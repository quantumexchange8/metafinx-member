<?php

namespace Database\Seeders;

use App\Models\SettingCoin;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    public function run(): void
    {
       SettingCoin::create([
           'name' => 'XLC Coin',
           'symbol' => 'XLC/MYR',
           'created_at' => Carbon::now(),
           'updated_at' => Carbon::now(),
       ]);
    }
}
