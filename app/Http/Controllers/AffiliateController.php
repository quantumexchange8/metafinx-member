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
        $totalReferralEarning = Earning::where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $totalAffiliateEarning = Earning::where('upline_id', \Auth::id())
            ->where('type', 'AffiliateEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $totalBinaryReferralEarning = Earning::where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $totalPairingEarning = Earning::where('upline_id', \Auth::id())
            ->where('type', 'PairingEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $downline = User::where('upline_id', \Auth::id())->with(['coinStaking'])->get();

        // Get the upline's ID
        $uplineId = User::where('id', \Auth::id())->value('upline_id');
        $uplineStaking = null;
        $userExist = CoinMultiLevel::where('user_id', \Auth::id())->exists();

        if ($uplineId && $userExist) {
            // If there is an upline and current user exist in table, check if they have a coin stacking record
            $uplineStaking = CoinMultiLevel::where('user_id', $uplineId)->exists();
        } elseif ($userExist) {
            $uplineStaking = true;
        } else{
            $uplineStaking = false;
        }

        return Inertia::render('Affiliate/Affiliate', [
            'referredCounts' => $referredCounts,
            'totalReferralEarning' => floatval($totalReferralEarning),
            'totalAffiliateEarning' => floatval($totalAffiliateEarning),
            'totalBinaryReferralEarning' => floatval($totalBinaryReferralEarning),
            'totalPairingEarning' => floatval($totalPairingEarning),
            'downline' => $downline,
            'uplineStaking' => $uplineStaking,
            'checkCoinStaking' => CoinStacking::where('user_id', Auth::id())->exists(),
        ]);
    }

    public function getTreeData(Request $request)
    {
        $searchUser = null;
        $searchTerm = $request->input('search');
        $childrenIds = Auth::user()->getChildrenIds();
        $childrenIds[] = Auth::id();

        if ($searchTerm) {

            $searchUser = User::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->whereIn('id', $childrenIds)
                ->first();

            if (!$searchUser) {
                return Auth::user();
            }

            if (!in_array($searchUser->id, $childrenIds)) {
                return Auth::user();
            }
        }

        $user = $searchUser ?? Auth::user();

        $users = User::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->whereIn('id', $childrenIds)->get();

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
        $binaryUser = CoinMultiLevel::with(['user:id,name,email,setting_rank_id', 'sponsor.user'])->where('user_id', Auth::id())->first();
        $binaryChildrenIds = $binaryUser->getChildrenIds();
        $binaryChildrenIds[] = $binaryUser->id;

        if ($searchTerm) {
            $searchUser = CoinMultiLevel::whereHas('user', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
                ->whereIn('id', $binaryChildrenIds)
                ->first();

            if (!$searchUser) {
                return $binaryUser;
            }

            if (!in_array($searchUser->id, $binaryChildrenIds)) {
                return $binaryUser;
            }
        }

        $user = $searchUser ?? $binaryUser;

        $users = CoinMultiLevel::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->whereIn('id', $binaryChildrenIds)->get();

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
            // 'sponsor_name' => $user->sponsor ? $user->sponsor->user->name : null,
            // 'sponsor_email' => $user->sponsor ? $user->sponsor->user->email : null,
            // 'sponsor_profile_photo' => $user->sponsor ? $user->sponsor->user->getFirstMediaUrl('profile_photo') : null,
            'email' => $user->user->email,
            'level' => $level,
            'rank' => $user->user->setting_rank_id,
            'personal_amount' => $this->getPersonalStakingAmount($user),
            'left_amount' => $this->getPairingPrice($user, 'left'),
            'right_amount' => $this->getPairingPrice($user, 'right'),
            'children' => $users->map(function ($user) {
                return $this->mapBinaryUser($user, 0);
            })
        ];

        // Ensure 'children' array contains two elements (left and right)
        if (count($binaryData['children']) == 1) {
            if ($binaryData['children'][0]['position'] == 'left') {
                $binaryData['children'] = [(object)$binaryData['children'][0], (object)null];
            } elseif ($binaryData['children'][0]['position'] == 'right') {
                $binaryData['children'] = [(object)null, (object)$binaryData['children'][0]];
            }
        } elseif (count($binaryData['children']) == 2) {
            if ($binaryData['children'][0]['position'] == 'left') {
                $binaryData['children'] = [(object)$binaryData['children'][0], (object)$binaryData['children'][1]];
            } else {
                $binaryData['children'] = [(object)$binaryData['children'][1], (object)$binaryData['children'][0]];
            }
        }

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
            'personal_amount' => $this->getPersonalStakingAmount($user),
            'left_amount' => $this->getPairingPrice($user, 'left'),
            'right_amount' => $this->getPairingPrice($user, 'right'),
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
        $sponsor = CoinMultiLevel::where('user_id', Auth::id())->first();
        $coinStakingPrice = CoinStacking::where('user_id', $request->user_id)->where('status', 'OnGoingPeriod')->sum('stacking_price');

        $directChild = $upline->direct_child($request->position)->first();
        // Ensure the specified position is either 'left' or 'right'
        $position = $request->position;

        // Update the hierarchy list based on the upline
        if ($upline->id == 1) {
            // If the upline is the root node, the hierarchy list will be the user's ID
            $hierarchyList = '-' . $request->upline_id . '-';
        } else {
            // Otherwise, prepend the upline's hierarchy list with a '-' if it's not empty
            $hierarchyList = $upline->hierarchy_list . $upline->id . '-';
        }

        $data = [
            'user_id' => $request->user_id,
            'sponsor_id' => $sponsor->id,
            'upline_id' => $upline->id,
            'hierarchy_list' => $hierarchyList,
            'position' => 'left', // default position
            'coin_stacking_amount' => $coinStakingPrice,
        ];

        if (empty($upline->direct_child('left')->first()) && empty($upline->direct_child('right')->first())) {
            if ($position == 'right' && $upline->id == $sponsor->id) {
                $data['position'] = $position;
            }
        } elseif ($position == 'right' && empty($directChild)) {
            $data['position'] = $position;
        }

        // Create the distributor with the provided parameters
        CoinMultiLevel::create($data);

        // Redirect back with success message
        return redirect()->back()->with('title', trans('public.affiliate.add_distributor'))->with('success', trans('public.affiliate.add_distributor_message'));
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

    protected function getPersonalStakingAmount($child)
    {
        return CoinStacking::where('user_id', $child->user_id)
            ->where('status', 'OnGoingPeriod')
            ->sum('stacking_price');
    }

    protected function getPairingPrice($child, $position)
    {
        $amount = 0;
        $totalEarning = 0;

        $directChild = $child->direct_child($position)->first();

        if ($directChild) {
            $ids = $directChild->getChildrenIds();

            $binaryUserId = CoinMultiLevel::query()
                ->whereIn('id', $ids)
                ->pluck('user_id')
                ->toArray();

            $lastPairingEarningDateTime = Earning::where('upline_id', $child->id)
                ->where('type', 'PairingEarnings')
                ->latest()
                ->value('created_at');

            $today = today();

            // Calculate amount and total earnings if lastPairingEarningDateTime is not empty
            if ($lastPairingEarningDateTime) {
                $amount = CoinStacking::whereIn('user_id', $binaryUserId)
                    ->where('status', 'OnGoingPeriod')
                    ->where('staking_date', '<', $today)
                    ->sum('stacking_price');

                $amount += CoinStacking::where('user_id', $directChild->user_id)
                    ->where('status', 'OnGoingPeriod')
                    ->where('staking_date', '<', $today)
                    ->sum('stacking_price');

                $totalEarning = Earning::where('upline_id', $directChild->user_id)
                    ->where('type', 'PairingEarnings')
                    ->where('created_at', '<', $lastPairingEarningDateTime)
                    ->sum('after_coin_price');

                $totalEarning += Earning::whereIn('upline_id', $binaryUserId)
                    ->where('type', 'PairingEarnings')
                    ->where('created_at', '<', $lastPairingEarningDateTime)
                    ->sum('after_coin_price');
            }

            // Calculate today's staking
            $todayStaking = CoinStacking::whereIn('user_id', $binaryUserId)
                ->where('status', 'OnGoingPeriod')
                ->whereDate('staking_date', $today)
                ->sum('stacking_price');

            $todayStaking += CoinStacking::where('user_id', $directChild->user_id)
                ->where('status', 'OnGoingPeriod')
                ->whereDate('staking_date', $today)
                ->sum('stacking_price');

            // Calculate stake pairing based on conditions
            return $lastPairingEarningDateTime ? $amount - $totalEarning + $todayStaking : $amount - $totalEarning;
        }

        return 0; // Return 0 if no direct child is found
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
        $existed_users_ids = CoinMultiLevel::get()->pluck('user_id');
        $childrenIds = $user->children()->get()->pluck('id');

        $users = User::query()
            ->where('role', 'user')
            ->where('auto_assign_at', '>=', now())
            ->whereDate('created_at', '>=', now()->subDay())
            ->whereIn('id', $childrenIds)
            ->whereNotIn('id', $existed_users_ids)
            ->when($request->filled('query'), function ($query) use ($request) {
                $search = $request->input('query');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email', 'created_at', 'auto_assign_at')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $users->each(function ($user) {
            $user->profile_photo = $user->getFirstMediaUrl('profile_photo');
        });

        return response()->json($users);
    }

    public function getLastChild(Request $request)
    {
        $user = Auth::user();
        $position = $request->position;
        $binaryAuthUser = CoinMultiLevel::where('user_id', $user->id)->first();
        $directChild = $binaryAuthUser->direct_child($position)->first();

        $last_child = $directChild ? $directChild->getLastChild($directChild, 'left') : $binaryAuthUser;
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

        return User::where('auto_assign_at', '>=', now())
            ->whereDate('created_at', '>=', now()->subDay())
            ->whereIn('id', $childrenIds)
            ->whereNotIn('id', $existedUsersIds)
            ->count();
    }

    public function getDistributorDetail(Request $request)
    {
        $binaryDetail = CoinMultiLevel::find($request->id);
        $currentStakingUnit = CoinStacking::where('user_id', $binaryDetail->user_id)
            ->where('status', 'OnGoingPeriod')
            ->sum('stacking_unit');

        $accruedStakingUnit = CoinStacking::where('user_id', $binaryDetail->user_id)
            ->where('status', 'MaturityPeriod')
            ->sum('stacking_unit');

        $currentStakingPrice = CoinStacking::where('user_id', $binaryDetail->user_id)
            ->where('status', 'OnGoingPeriod')
            ->sum('stacking_price');

        $accruedStakingPrice = CoinStacking::where('user_id', $binaryDetail->user_id)
            ->where('status', 'MaturityPeriod')
            ->sum('stacking_price');

        $detail = [
            'name' => $binaryDetail->user->name,
            'email' => $binaryDetail->user->email,
            'profile_photo' => $binaryDetail->user->getFirstMediaUrl('profile_photo') ?? null,
            // 'sponsor_name' => $binaryDetail->sponsor ? $binaryDetail->sponsor->user->name : null,
            // 'sponsor_email' => $binaryDetail->sponsor ? $binaryDetail->sponsor->user->email : null,
            // 'sponsor_profile_photo' => $binaryDetail->sponsor ? $binaryDetail->sponsor->user->getFirstMediaUrl('profile_photo') : null,
            'upline_name' => $binaryDetail->upline ? $binaryDetail->upline->user->name : null,
            'upline_email' => $binaryDetail->upline ? $binaryDetail->upline->user->email : null,
            'upline_profile_photo' => $binaryDetail->upline ? $binaryDetail->upline->user->getFirstMediaUrl('profile_photo') : null,
            'level' => $request->level,
            'current_staking' => number_format($currentStakingUnit, 4) . ' MXT ($ ' . number_format($currentStakingPrice, 2) . ')',
            'accrued_staking' => number_format($accruedStakingUnit, 4) . ' MXT ($ ' . number_format($accruedStakingPrice, 2) . ')'
        ];

        return response()->json($detail);
    }

}
