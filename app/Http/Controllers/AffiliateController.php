<?php

namespace App\Http\Controllers;

use App\Models\InvestmentSubscription;
use App\Models\User;
use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        $query = User::whereHas('upline', function ($query) use ($user) {
            $query->where('id', $user->id);
        });

        if ($searchUser) {
            $query->orWhere('id', $searchUser->id);
        }

        $users = $query->get();

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
}
