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
        // Retrieve data which is 'auto_assign_at' value not yet passed today
        $coinStackings = CoinStacking::whereDate('auto_assign_at', '=', now())->get();
    
        $coinStackings = $coinStackings->sortBy('user_id');

        foreach ($coinStackings as $coinStacking) {
            $user = $coinStacking->user;
    
            // Check if data does not exist in CoinMultiLevel table for the user
            if (!CoinMultiLevel::where('user_id', $user->id)->exists()) {
                $auto_assign_at = $coinStacking->auto_assign_at;
    
                // Find the direct left child
                $directChild = CoinMultiLevel::where('position', 'left')->first();
    
                // Check if direct left child exists
                if ($directChild) {
                    // Find the last left child in the hierarchy list
                    $lastChild = CoinMultiLevel::where('hierarchy_list', 'LIKE', '%' . $directChild->hierarchy_list . '%')
                        ->where('position', 'left')
                        ->orderBy('id', 'desc')
                        ->first();
    
                    // Check if last child exists and is not the same as the current user
                    if ($lastChild && $lastChild->id !== $user->id) {
                        $currentHierarchyList = $lastChild->hierarchy_list . $lastChild->id . '-';
    
                        // Distributor creation logic
                        $upline = $lastChild;
                        $coinStakingPrice = CoinStacking::where('user_id', $user->id)->where('status', 'OnGoingPeriod')->sum('stacking_price');
    
                        CoinMultiLevel::create([
                            'user_id' => $user->id,
                            'sponsor_id' => $upline->id,
                            'upline_id' => $upline->id,
                            'hierarchy_list' => $currentHierarchyList,
                            'position' => 'left', // Assuming position is always left for auto-placement
                            'coin_stacking_amount' => $coinStakingPrice,
                        ]);
    
                        $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto-assigned at: {$auto_assign_at} under Upline ID: {$upline->id}, HierarchyList: {$currentHierarchyList}");
                    } else {
                        $this->info("No valid upline found for User ID: {$user->id}, Name: {$user->name}.");
                    }
                } else {
                    // If there are no direct left children, create the first entry in the hierarchy
                    $existingEntry = CoinMultiLevel::orderBy('id', 'asc')->first();
                    $hierarchyList = '-' . $existingEntry->id . '-';
                    $coinStakingPrice = CoinStacking::where('user_id', $user->id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

                    CoinMultiLevel::create([
                        'user_id' => $user->id,
                        'sponsor_id' => $existingEntry->id, // Set sponsor ID to the ID of the existing entry
                        'upline_id' => $existingEntry->id,  // Set upline ID to the ID of the existing entry
                        'hierarchy_list' => $hierarchyList,
                        'position' => 'left', // Assuming position is always left for auto-placement
                        'coin_stacking_amount' => $coinStakingPrice,
                    ]);
    
                    $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto-assigned at: {$auto_assign_at} under Upline ID: {$existingEntry->id}, HierarchyList: {$hierarchyList}");
                }
            } else {
                $this->info("User ID: {$user->id}, Name: {$user->name}, already exists in CoinMultiLevel table.");
            }
        }
    
        $this->info('Auto placement check completed.');
    }
}
