<?php

namespace App\Console\Commands;

use App\Models\InvestmentSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateSubscriptionCommand extends Command
{
    protected $signature = 'update:subscription';

    protected $description = 'Update subscription to OnGoingPeriod';

    public function handle(): void
    {
        $subscriptions = InvestmentSubscription::where('status', 'CoolingPeriod')->get();

        foreach ($subscriptions as $subscription) {
            $cooling_period_date = $subscription->created_at->addDays(60)->startOfDay();
            $update_date = date_format($cooling_period_date, 'Y-m-d');
            $current_date = Carbon::now()->format('Y-m-d');

            if ($update_date == $current_date) {
                $subscription->update([
                    'status' => 'OnGoingPeriod'
                ]);
            }
        }
    }
}
