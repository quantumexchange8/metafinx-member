<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\CoinStacking;
use App\Models\CoinMultiLevel;

class CheckAutoPlacementCommand extends Command
{
    protected $signature = 'check:auto-placement';

    protected $description = 'Check auto placement and retrieve information about users without placement, then assign placement for them.';

    public function handle(): void
    {
        // Retrieve data which is 'auto_assign_at' value not yet passed today
        $users = User::whereDate('auto_assign_at', '=', now())->get();

        foreach ($users as $user) {
            // Check if data does not exist in CoinMultiLevel table for the user
            if (!CoinMultiLevel::where('user_id', $user->id)->exists()) {
                $auto_assign_at = $user->auto_assign_at;

                // Find the direct left child of his upline
                $directChild = CoinMultiLevel::where('user_id', $user->upline_id)->first();

                // Check if direct left child exists
                if ($directChild) {
                    $directLastChild = $directChild->direct_child('left')->first();

                    $lastChild = $directLastChild ? $directLastChild->getLastChild($directLastChild, 'left') : $directChild;

                    // Check if last child exists and is not the same as the current user
                    if ($lastChild && $lastChild->id !== $user->id) {
                        $currentHierarchyList = $lastChild->hierarchy_list . $lastChild->id . '-';

                        // Distributor creation logic
                        $upline = $lastChild;
                        $userUpline = $user->upline;
                        $binarySponsor = CoinMultiLevel::where('user_id', $userUpline->id)->first();

                        $coinStakingPrice = CoinStacking::where('user_id', $user->id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

                        CoinMultiLevel::create([
                            'user_id' => $user->id,
                            'sponsor_id' => $binarySponsor->id,
                            'upline_id' => $upline->id,
                            'hierarchy_list' => $currentHierarchyList,
                            'position' => 'left', // Assuming position is always left for auto-placement
                            'coin_stacking_amount' => $coinStakingPrice,
                        ]);

                        $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto-assigned at: {$auto_assign_at} under Upline ID: {$upline->id}, HierarchyList: {$currentHierarchyList}, Sponsor ID: {$binarySponsor->id}");
                    } else {
                        $currentHierarchyList = $directChild->hierarchy_list . $directChild->id . '-';

                        // Distributor creation logic
                        $userUpline = $user->upline;
                        $binarySponsor = CoinMultiLevel::where('user_id', $userUpline->id)->first();

                        $coinStakingPrice = CoinStacking::where('user_id', $user->id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

                        CoinMultiLevel::create([
                            'user_id' => $user->id,
                            'sponsor_id' => $binarySponsor->id,
                            'upline_id' => $directChild->id,
                            'hierarchy_list' => $currentHierarchyList,
                            'position' => 'left', // Assuming position is always left for auto-placement
                            'coin_stacking_amount' => $coinStakingPrice,
                        ]);

                        $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto-assigned at: {$auto_assign_at} under Upline ID: {$binarySponsor->id}, HierarchyList: {$currentHierarchyList}, Sponsor ID: {$binarySponsor->id}");
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

                    $this->info("User ID: {$user->id}, Name: {$user->name}, will be auto-assigned at: {$auto_assign_at} under Upline ID: {$existingEntry->id}, HierarchyList: {$hierarchyList} where there is no direct LEFT child");
                }
            } else {
                $this->info("User ID: {$user->id}, Name: {$user->name}, already exists in CoinMultiLevel table.");
            }
        }

        $this->info('Auto placement check completed.');
    }
}
