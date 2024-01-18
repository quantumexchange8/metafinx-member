<?php

namespace App\Http\Controllers;

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
        $totalReferralEarning = Earning::where('upline_id', \Auth::id())->where('type', 'referral_earnings')->sum('after_amount');

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
