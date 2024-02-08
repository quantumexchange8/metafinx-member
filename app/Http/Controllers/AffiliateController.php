<?php

namespace App\Http\Controllers;

use App\Models\CoinMultiLevel;
use App\Models\CoinStacking;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Earning;
use App\Models\SettingRank;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AffiliateTableExport;
use App\Models\InvestmentSubscription;

class AffiliateController extends Controller
{
    public function referral_view()
    {
        $referredCounts = User::where('upline_id', \Auth::id())->count();
        $totalReferralEarning = Earning::where('upline_id', \Auth::id())->where('type', 'ReferralEarnings')->sum('after_amount');

        $downline = User::where('upline_id', \Auth::id())->with(['coinStaking'])->get();

        // Get the upline's ID
        $uplineId = User::where('id', \Auth::id())->value('upline_id');
        $uplineStaking = true;

        if ($uplineId) {
            // If there is an upline, check if they have a coin stacking record
            $uplineStaking = CoinStacking::where('user_id', $uplineId)->exists();
        }

        return Inertia::render('Affiliate/Affiliate', [
            'referredCounts' => $referredCounts,
            'totalReferralEarning' => floatval($totalReferralEarning),
            'downline' => $downline,
            'uplineStaking' => $uplineStaking,
        ]);
    }
        public function getTreeData(Request $request)
    {
        $searchUser = null;
        $searchTerm = $request->input('search');

        if ($searchTerm) {
            $searchUser = User::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->first();

            if (!$searchUser) {
                return response()->json(['error' => 'User not found for the given search term.'], 404);
            }
        }

        $user = $searchUser ?? Auth::user();

        $users = User::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

//        if ($searchUser) {
//            $query->orWhere('id', $searchUser->id);
//        }
//
//        $users = $query->get();

        $level = 0;
        $rootNode = [
            'name' => $user->name,
            'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
            'email' => $user->email,
            'level' => $level,
            'total_affiliate' => count($user->getChildrenIds()),
            'rank' => $user->setting_rank_id,
            'self_deposit' => $this->getSelfDeposit($user),
            'valid_affiliate_deposit' => $this->getValidAffiliateDeposit($user),
            'children' => $users->map(function ($user) {
                return $this->mapUser($user, 0);
            })
        ];

        return response()->json($rootNode);
    }

    protected function mapUser($user, $level) {
        $children = $user->children;

        $mappedChildren = $children->map(function ($child) use ($level) {
            return $this->mapUser($child, $level + 1);
        });

        $mappedUser = [
            'name' => $user->name,
            'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
            'email' => $user->email,
            'level' => $level + 1,
            'total_affiliate' => count($user->getChildrenIds()),
            'rank' => $user->setting_rank_id,
            'self_deposit' => $this->getSelfDeposit($user),
            'valid_affiliate_deposit' => $this->getValidAffiliateDeposit($user),
        ];

        // Add 'children' only if there are children
        if (!$mappedChildren->isEmpty()) {
            $mappedUser['children'] = $mappedChildren;
        }

        return $mappedUser;
    }

    public function getBinaryData(Request $request)
    {
        $searchUser = null;
        $searchTerm = $request->input('search');

        if ($searchTerm) {
            // dd('asdasd');
            $searchUser = User::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->first();

            if (!$searchUser) {
                return response()->json(['error' => 'User not found for the given search term.'], 404);
            }
        }

        $user = $searchUser ?? CoinMultiLevel::with(['user:id,name,email,setting_rank_id', 'sponsor.user'])->where('user_id', Auth::id())->first();


        $users = CoinMultiLevel::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

//        if ($searchUser) {
//            $query->orWhere('id', $searchUser->id);
//        }
//
//        $users = $query->get();

        $level = 0;
        $binaryData = [
            'id' => $user->id,
            'name' => $user->user->name,
            'profile_photo' => $user->user->getFirstMediaUrl('profile_photo'),
            'position' => $user->position,
            'sponsor_name' => $user->sponsor ? $user->sponsor->user->name : null,
            'sponsor_email' => $user->sponsor ? $user->sponsor->user->email : null,
            'sponsor_profile_photo' => $user->sponsor ? $user->sponsor->user->getFirstMediaUrl('profile_photo') : null,
            'email' => $user->user->email,
            'level' => $level,
            'rank' => $user->user->setting_rank_id,
            'personal_amount' => $user->coin_stacking_amount,
            'left_amount' => $this->getTotalAmount($user, 'left'),
            'right_amount' => $this->getTotalAmount($user, 'right'),
            'children' => $users->map(function ($user) {
                return $this->mapBinaryUser($user, 0);
            })
        ];

        return response()->json($binaryData);
    }

    protected function mapBinaryUser($user, $level)
    {
        $children = $user->children;
        $childrenCount = count($children);

        $mappedChildren = $children->map(function ($child) use ($level) {
            return $this->mapBinaryUser($child, $level + 1);
        });

        $mappedUser = [
            'id' => $user->id,
            'name' => $user->user->name,
            'profile_photo' => $user->user->getFirstMediaUrl('profile_photo'),
            'position' => $user->position,
            'sponsor' => $user->sponsor->user->name ?? null,
            'email' => $user->user->email,
            'level' => $level + 1,
            'rank' => $user->user->setting_rank_id,
            'personal_amount' => $user->coin_stacking_amount,
            'left_amount' => $this->getLeftAmount($user),
            'right_amount' => $this->getRightAmount($user),
        ];

        // Add 'children' only if there are children
        if (!$mappedChildren->isEmpty()) {
            // Separate children into 'left' and 'right' arrays
            $leftChildren = [];
            $rightChildren = [];

            foreach ($mappedChildren as $mapChild) {
                if ($mapChild['position'] == 'left') {
                    $leftChildren[] = (object)$mapChild;
                } else {
                    $rightChildren[] = (object)$mapChild;
                }
            }

            // Handle the case of a single child
            if (count($mappedChildren) == 1) {
                if ($leftChildren) {
                    $mappedUser['children'] = [(object)$leftChildren[0], (object)null];
                } elseif ($rightChildren) {
                    $mappedUser['children'] = [(object)null, (object)$rightChildren[0]];
                }
            } else {
                // Merge 'left' and 'right' children into the 'children' array
                $mappedUser['children'] = array_merge($leftChildren, $rightChildren);
            }
        } else {
            $mappedUser['children'] = [];
        }

        return $mappedUser;
    }


    public function getAvailableDistributor(Request $request)
    {
        $existed_users_ids = CoinMultiLevel::get()->pluck('user_id');

        $users = User::query()
            ->where('role', '=', 'user')
            ->whereNotIn('id', $existed_users_ids)
            ->when($request->filled('query'), function ($query) use ($request) {
                $search = $request->input('query');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email')
            ->get();

        $users->each(function ($users) {
            $users->profile_photo = $users->getFirstMediaUrl('profile_photo');
        });

        return response()->json($users);
    }

    public function addDistributor(Request $request)
    {
        $upline = CoinMultiLevel::find($request->upline_id);
        $coinStakingPrice = CoinStacking::where('user_id', $request->user_id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

        // Ensure the specified position is either 'left' or 'right'
        $position = ($request->position === 'left' || $request->position === 'right') ? $request->position : 'left';

        // Update the hierarchy list based on the upline
        if ($upline->id == 1) {
            // If the upline is the root node, the hierarchy list will be the user's ID
            $hierarchyList = '-' . $request->upline_id . '-';
        } else {
            // Otherwise, prepend the upline's hierarchy list with a '-' if it's not empty
            $hierarchyList = $upline->hierarchy_list . $upline->id . '-';
        }

        // Create the distributor with the provided parameters
        CoinMultiLevel::create([
            'user_id' => $request->user_id,
            'sponsor_id' => Auth::id(),
            'upline_id' => $upline->id,
            'hierarchy_list' => $hierarchyList,
            'position' => $position,
            'coin_stacking_amount' => $coinStakingPrice,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('title', 'Add Distributor')->with('success', 'Distributor has been successfully added!');
    }

    protected function getSelfDeposit($user)
    {
        return InvestmentSubscription::query()
            ->where('user_id', $user->id)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    protected function getValidAffiliateDeposit($user)
    {
        $ids = $user->getChildrenIds();

        return InvestmentSubscription::query()
            ->whereIn('user_id', $ids)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    protected function getLeftAmount($child)
    {
        $ids = $child->getChildrenIds();

        return CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->where('position', 'left')
            ->sum('coin_stacking_amount');
    }

    protected function getRightAmount($child)
    {
        $ids = $child->getChildrenIds();

        return CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->where('position', 'right')
            ->sum('coin_stacking_amount');
    }

    protected function getTotalAmount($child, $uplinePosition)
    {
        // Initialize left and right amounts
        $leftAmount = 0;
        $rightAmount = 0;
    
        // Get the IDs of direct downline based on the $uplinePosition
        $downlineIds = CoinMultiLevel::query()
            ->where('upline_id', $child->id)
            ->where('position', $uplinePosition)
            ->pluck('id')
            ->toArray();
    
        // Calculate amount for each direct downline
        foreach ($downlineIds as $downlineId) {
            $downline = CoinMultiLevel::find($downlineId);
    
            if (!$downline) {
                continue;
            }
    
            // Add the direct downline amount
            if ($downline->position === 'left') {
                $leftAmount += $downline->coin_stacking_amount;
            } elseif ($downline->position === 'right') {
                $rightAmount += $downline->coin_stacking_amount;
            }
    
            // Get all children IDs under the direct downline
            $allChildrenIds = CoinMultiLevel::query()
                ->where('upline_id', $downlineId)
                ->pluck('id')
                ->toArray();
    
            // Calculate amount for all children under the direct downline
            foreach ($allChildrenIds as $childId) {
                $child = CoinMultiLevel::find($childId);
    
                if (!$child) {
                    continue;
                }
    
                // Add the child amount
                if ($child->position === 'left') {
                    $leftAmount += $child->coin_stacking_amount;
                } elseif ($child->position === 'right') {
                    $rightAmount += $child->coin_stacking_amount;
                }
            }
        }
    
        // Return only the appropriate amount based on the upline position
        if ($uplinePosition === 'left') {
            return $leftAmount;
        } elseif ($uplinePosition === 'right') {
            return $rightAmount;
        }
    }
    
    public function group()
    {
        $referredCounts = User::where('upline_id', \Auth::id())->count();

        $user = Auth::user();

        $validAffiliateDeposit = $this->getValidAffiliateDeposit($user);
        $totalAffiliate = count($user->getChildrenIds());

        // Calculate the highest generation directly in the group function
        $highestGeneration = 0;

        foreach ($user->getChildrenIds() as $childId) {
            $child = User::find($childId); // Assuming User model has a 'find' method
            $level = substr_count($child->hierarchyList, '-') - 1;
            $highestGeneration = max($highestGeneration, $level);
        }

        return Inertia::render('Affiliate/MyGroup', [
            'referredCounts' => $referredCounts,
            'validAffiliateDeposit' => $validAffiliateDeposit,
            'totalAffiliate' => $totalAffiliate,
            'totalGeneration' => $highestGeneration,
        ]);
    }

    public function getReferralTableData(Request $request)
    {
        $user = Auth::user();

        $childrenIds = $user->getChildrenIds();
        $childrenData = User::whereIn('id', $childrenIds);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $childrenData->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->orWhere('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('upline', function ($uplineQuery) use ($search) {
                        $uplineQuery->where('name', 'like', $search)
                                    ->orWhere('email', 'like', $search);
                    });
            });
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
            $childrenData->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($request->filled('rank')) {
            $rank = $request->input('rank');
            $childrenData->where('setting_rank_id', $rank);
        }

        if ($request->filled('level')) {
            $level = $request->input('level');
            $childrenData->where(function ($query) use ($level) {
                $query->whereRaw("LENGTH(hierarchyList) - LENGTH(REPLACE(hierarchyList, '-', '')) = ?", [$level + 1]);
            });
        }

        if ($request->has('exportStatus')) {
            return Excel::download(new AffiliateTableExport($childrenData), 'affiliate_table.xlsx');
        }

        $children_data = $childrenData->latest()->paginate(10);

        $children_data->getCollection()->transform(function ($child) use ($user) {
            $upline = $child->upline;
            return [
                    'id' => $child->id,
                    'profile_photo' => $child->getFirstMediaUrl('profile_photo') ?? null,
                    'name' => $child->name,
                    'email' => $child->email,
                    'upline_id' => $child->upline->id ?? null,
                    'upline_profile_photo' => $upline ? $upline->getFirstMediaUrl('profile_photo') ?? null : null,
                    'upline_name' => $child->upline->name ?? null,
                    'upline_email' => $child->upline->email ?? null,
                    'created_at' => $child->created_at,
                    'setting_rank_id' => $child->setting_rank_id,
                    'setting_rank_name' => $this->getSettingRankName($child->setting_rank_id),
                    'level' => $child->id === $user->id ? 0 : ($child->getLevel() - 1),
                    'total_affiliate' => count($child->getChildrenIds()),
                    'valid_self_deposit' => $this->getSelfDeposit($child),
                    'valid_affiliate_deposit' => $this->getValidAffiliateDeposit($child),
            ];
        });

        return response()->json($children_data);
    }

    protected function getSettingRankName($settingRankId)
    {
        $settingRank = SettingRank::find($settingRankId);

        return $settingRank ? $settingRank->name : null;
    }

    public function getAvailableBinaryAffiliate(Request $request)
    {
        $user = Auth::user();
        $existedUserIds = CoinMultiLevel::pluck('user_id');
        $childrenIds = $user->children()->pluck('id');

        $query = User::with(['coinStaking', 'media'])
            ->whereIn('id', $childrenIds)
            ->where('role', 'user')
            ->whereNotIn('id', $existedUserIds)
            ->when($request->filled('query'), function ($query) use ($request) {
                $search = $request->input('query');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Transform each user to include only the specified attributes
        $transformedUsers = $query->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
                'coin_staking' => $user->coinStaking,
                'media' => $user->media->toArray(),
            ];
        });

        // Replace the items in the paginated results with the transformed users
        $query->setCollection($transformedUsers);

        return response()->json($query);
    }

    public function getLastChild(Request $request)
    {
        $user = Auth::user();
        $position = $request->position;
        $binaryAuthUser = CoinMultiLevel::where('user_id', $user->id)->first();

        $last_child = $binaryAuthUser->getLastChild($position);
        if ($last_child) {
            $last_child->profile_photo = $last_child->user->getFirstMediaUrl('profile_photo');
        }

        return response()->json($last_child);
    }

    public function getPendingPlacementCount()
    {
        $user = Auth::user();
        $childrenIds = $user->children()->pluck('id')->toArray();
        $existedUsersIds = CoinMultiLevel::pluck('user_id')->toArray();

        return CoinStacking::where('auto_assign_at', '>=', now())
            ->whereDate('created_at', '>=', now()->subDay())
            ->whereIn('user_id', $childrenIds)
            ->whereNotIn('user_id', $existedUsersIds)
            ->distinct('user_id')
            ->count();
    }

    public function checkCoinStackingExistence()
    {
        $userId = Auth::id();

        // Check if the user exists in the coin stacking table
        $exists = CoinStacking::where('user_id', $userId)->exists();

        return response()->json($exists);
    }

}
