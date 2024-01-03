<?php

namespace Database\Seeders;

use App\Models\CoinPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CoinPriceSeeder extends Seeder
{
    public function run(): void
    {
        CoinPrice::create([
            'setting_coin_id' => 1,
            'updated_by' => 1,
            'price' => 0.67,
            'price_date' => Carbon::now()->addDay(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
