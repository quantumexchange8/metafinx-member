<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CoinStacking;
use App\Models\CoinMultiLevel;

class CheckAutoPlacementCommand extends Command
{
    protected $signature = 'check:auto-placement';

    protected $description = 'Check auto placement and retrieve information about users without placement, then assign placement for them.';

    public function handle()
    {
        // Retrieve data which is 'auto_assign_at' value not yet pass today
        $coinStackings = CoinStacking::whereDate('auto_assign_at', '<=', now())->get();

        foreach ($coinStackings as $coinStacking) {
            $user = $coinStacking->user;

            $existCoinMultiLevel = CoinMultiLevel::where('user_id', $user->id)->first();

            // Check if data does not exist in CoinMultiLevel table
            if (!$existCoinMultiLevel) {
                $auto_assign_at = $coinStacking->auto_assign_at;

                $directChild = CoinMultiLevel::where('position', 'left')->first();

                // Check if direct left child and child exist
                if ($directChild && $child = $directChild->children()->where('position', 'left')->latest()->first()) {
                    $lastChild = CoinMultiLevel::where('hierarchy_list', 'LIKE', '%' . $child->hierarchy_list . '%')
                        ->with('user:id,name,email')
                        ->whereHas('upline', function ($query) {
                            $query->where('position', 'left');
                        })
                        ->where('position', 'left')
                        ->orderBy('id', 'desc')
                        ->first();

                    // Check if last child is the upline for the current user
                    if ($lastChild && $lastChild->id !== $user->id) {
                        $currentHierarchyList = $lastChild->hierarchy_list. $lastChild->id . '-';

                        // Distributor creation logic
                        $upline = CoinMultiLevel::find($lastChild->id);
                        $coinStakingPrice = CoinStacking::where('user_id', $user->id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

                        CoinMultiLevel::create([
                            'user_id' => $user->id,
                            'sponsor_id' => $upline->id,
                            'upline_id' => $upline->id,
                            'hierarchy_list' => $upline->hierarchy_list . $upline->id . "-",
                            'position' => 'left',
                            'coin_stacking_id' => $coinStacking->id,
                            'coin_stacking_amount' => $coinStakingPrice,
                        ]);

                        $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto assigned at: {$auto_assign_at} under Upline ID: {$lastChild->id}, HierarchyList: {$currentHierarchyList}");
                    } else {
                        $this->info("No valid upline found for User ID: {$user->id}, Name: {$user->name}.");
                    }
                } else {
                    $this->info("No direct left child found for User ID: {$user->id}, Name: {$user->name}.");
                }
            } else {
                $this->info("User ID: {$user->id}, Name: {$user->name}, already exists in CoinMultiLevel table.");
            }
        }

        $this->info('Auto placement check completed.');
    }
}
