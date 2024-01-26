<?php

namespace App\Http\Controllers;

use App\Models\CoinMultiLevel;
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
        $totalReferralEarning = Earning::where('upline_id', \Auth::id())->where('type', 'ReferralEarning')->sum('after_amount');

        return Inertia::render('Affiliate/Affiliate', [
            'referredCounts' => $referredCounts,
            'totalReferralEarning' => floatval($totalReferralEarning),
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
            dd('asdasd');
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
            'sponsor' => $user->sponsor->user->name ?? null,
            'email' => $user->user->email,
            'level' => $level,
            'rank' => $user->user->setting_rank_id,
            'personal_amount' => $user->coin_stacking_amount,
            'left_amount' => $this->getLeftTotalAmount($user),
            'right_amount' => $this->getRightTotalAmount($user),
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
            'sponsor' => $user->sponsor->name ?? null,
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

        $hierarchyList = $upline->hierarchy_list . $upline->id . "-";

        CoinMultiLevel::create([
            'user_id' => $request->user_id,
            'sponsor_id' => Auth::id(),
            'upline_id' => $upline->id,
            'hierarchy_list' => $hierarchyList,
            'position' => $request->position,
        ]);

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

    protected function getLeftTotalAmount($child)
    {
        $ids = $child->getChildrenIds();

        $leftAmount = CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->whereHas('upline', function ($query) {
                $query->where('position', 'left');
            })
            ->sum('coin_stacking_amount');

        $rightAmount = CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->whereHas('upline', function ($query) {
                $query->where('position', 'left');
            })
            ->where('position', 'right')
            ->sum('coin_stacking_amount');

        $leftPosition = $child->position;
        if ($leftPosition == 'left') {
            return $leftAmount;
        } elseif ($leftPosition == 'right') {
            return $rightAmount;
        } else {
            return $leftAmount + $rightAmount;
        }
    }
    protected function getRightTotalAmount($child)
    {
        $ids = $child->getChildrenIds();

        $leftAmount = CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->whereHas('upline', function ($query) {
                $query->where('position', 'right');
            })
            ->where('position', 'left')
            ->sum('coin_stacking_amount');

        $rightAmount = CoinMultiLevel::query()
            ->whereIn('id', $ids)
            ->whereHas('upline', function ($query) {
                $query->where('position', 'right');
            })
            ->sum('coin_stacking_amount');

        $leftPosition = $child->position;
        if ($leftPosition == 'right') {
            return $leftAmount;
        } elseif ($leftPosition == 'left') {
            return $rightAmount;
        } else {
            return $leftAmount + $rightAmount;
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

}
