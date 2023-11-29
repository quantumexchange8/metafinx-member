<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentSubscription;

class AffiliateController extends Controller
{
    public function my_group()
    {
        $user = \Auth::user();

        $descendants = $user->getDescendants();

        // Organize descendants by generations
        $groupedDescendants = $descendants->groupBy(function ($item) {
            return 'Gen ' . ($item->getLevel() - 1);
        })->map(function ($generation) {
            return $generation->map(function ($item) {
                return [
                    'image_address' => $item->getFirstMediaUrl('profile_photo'),
                    'name' => $item->name,
                    'email' => $item->email,
                    'ranking' => $item->setting_rank_id,
                    'upline' => $item->upline ? $item->upline->name : null,
                    'direct_referrals' => count($item->children),
                    'total_affiliates' => count($item->getChildrenIds()),
                    'vsd' => $this->getSelfDeposit($item),
                    'vad' => $this->getValidAffiliateDeposit($item),
                    'internal_wallet_balance' => $item->wallets[0]->balance,
                ];
            });
        });

        return response()->json($groupedDescendants);
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
