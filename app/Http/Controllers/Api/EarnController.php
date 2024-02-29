<?php

namespace App\Http\Controllers\Api;

use Notification;
use Carbon\Carbon;
use App\Models\Coin;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\CoinPrice;
use App\Models\SettingCoin;
use App\Models\Transaction;
use App\Models\CoinStacking;
use Illuminate\Http\Request;
use App\Models\InvestmentPlan;
use App\Http\Controllers\Controller;
use App\Models\InvestmentSubscription;
use App\Services\RunningNumberService;
use App\Notifications\StakingNotification;
use Illuminate\Validation\ValidationException;

class EarnController extends Controller
{
    public function investment_plans()
    {
        $investment_plans = InvestmentPlan::query()
            ->with('descriptions:investment_plan_id,description')
            ->where('status', 'active')
            ->select('id', 'name', 'roi_per_annum', 'type', 'commision_multiplier')
            ->get();

        $groupedInvestmentPlans = $investment_plans->groupBy('type');

        $translatedInvestmentPlans = $groupedInvestmentPlans->map(function ($group, $type) {
            // Select the fields you want for each group
            return [
                'type' => $type,
                'plans' => $group->map(function ($investmentPlan) {
                    return [
                        'id' => $investmentPlan->id,
                        'name' => $investmentPlan->getTranslation('name', app()->getLocale()),
                        'roi_per_annum' => $investmentPlan->roi_per_annum,
                        'commision_multiplier' => $investmentPlan->commision_multiplier,
                        'descriptions' => $investmentPlan->descriptions->map(function ($description) {
                            return [
                                'description' => $description->getTranslation('description', app()->getLocale()),
                            ];
                        }),
                        'type' => $investmentPlan->type,
                        'media' => [
                            'stacking' => optional($investmentPlan->getMedia('stacking_plan')->first())->getUrl(),
                            'standard' => optional($investmentPlan->getMedia('standard_plan')->first())->getUrl(),
                        ],
                    ];
                }),
            ];
        });

        return response()->json($translatedInvestmentPlans);
    }

    public function my_investments()
    {
        $user = \Auth::user();

        // Get InvestmentSubscriptions
        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period,type')
            ->where('user_id', $user->id)
            ->get();

        // Get CoinStackings
        $stackings = CoinStacking::query()
            ->with('investment_plan:id,name,investment_period,type')
            ->where('user_id', $user->id)
            ->get();

        $locale = app()->getLocale(); // Get the current locale

        // Map InvestmentSubscriptions
        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => $investmentSubscription->investment_plan->getTranslation('name', $locale),
                'roi_per_annum' => $investmentSubscription->investment_plan->roi_per_annum,
                'investment_period' => $investmentSubscription->investment_plan->investment_period,
                'subscription_id' => $investmentSubscription->subscription_id,
                'type' => $investmentSubscription->investment_plan->type,
                'amount' => $investmentSubscription->amount,
                'total_earning' => $investmentSubscription->total_earning,
                'status' => $investmentSubscription->status,
                'next_roi_date' => $investmentSubscription->next_roi_date,
                'expired_date' => $investmentSubscription->expired_date,
                'created_at' => $investmentSubscription->created_at,
            ];
        });

        // Map CoinStackings
        $coinStackings = $stackings->map(function ($coinStacking) use ($locale) {
            return [
                'id' => $coinStacking->id,
                'plan_name' => $coinStacking->investment_plan->getTranslation('name', $locale),
                'investment_period' => $coinStacking->investment_plan->investment_period,
                'subscription_id' => $coinStacking->subscription_number,
                'type' => $coinStacking->investment_plan->type,
                'amount' => $coinStacking->stacking_unit,
                'link_price' => $coinStacking->stacking_price,
                'total_earning' => $coinStacking->total_earning,
                'status' => $coinStacking->status,
                'next_roi_date' => $coinStacking->next_roi_date,
                'expired_date' => $coinStacking->expired_date,
                'created_at' => $coinStacking->created_at,
                'max_capped_price' => $coinStacking->max_capped_price,
            ];
        });

        return response()->json([
            'standard_subscription' => $investmentSubscriptions,
            'staking_subscription' => $coinStackings,
        ]);
    }

    public function subscribe(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'wallet_id' => ['sometimes', 'required'],
            'amount' => ['required', 'numeric'],
            'unit' => ['required', 'numeric'],
            'unit_number' => ['sometimes', 'required'],
            'housing_price' => ['sometimes', 'required', 'numeric', 'integer'],
            'terms' => ['accepted'],
        ])->setAttributeNames([
            'wallet_id' => trans('public.wallet.wallet'),
            'amount' => trans('public.wallet.amount'),
            'unit' => trans('public.wallet.unit'),
            'unit_number' => trans('public.earn.unit_number'),
            'housing_price' => trans('public.earn.housing_price'),
            'terms' => trans('public.earn.t&c'),
        ]);

        if (!$validator->passes()){
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = \Auth::user();
            $investment_plan = InvestmentPlan::find($request->investment_plan_id);
            $wallet = Wallet::find($request->wallet_id);
            $amount = $request->amount;

            switch($investment_plan->type) {
                case('standard'):
    
                    if ($amount % 100 !== 0) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Please enter an amount in increments of 100.'
                        ]);
                    }

                    if ($amount < $investment_plan->investment_min_amount) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Amount minimum is $ ' . $investment_plan->investment_min_amount
                        ]);
                    }

                    if ($wallet->balance < $amount) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Insufficient Balance'
                        ]);
                    } else {
                        $updated_balance = $wallet->balance - $amount;

                        $wallet->update([
                            'balance' => $updated_balance
                        ]);
                    }

                    $subscription_id = RunningNumberService::getID('investment');

                    $transaction = Transaction::create([
                        'category' => 'wallet',
                        'user_id' => $user->id,
                        'transaction_type' => 'Investment',
                        'from_wallet_id' => $request->wallet_id,
                        'transaction_number' => $subscription_id,
                        'amount' => $amount,
                        'transaction_charges' => 0,
                        'transaction_amount' => $amount,
                        'status' => 'Success',
                        'new_wallet_amount' => $wallet->balance,
                    ]);
            
                    $investmentSubscription = InvestmentSubscription::create([
                        'user_id' => $user->id,
                        'wallet_id' => $request->wallet_id,
                        'transaction_id' => $transaction->id,
                        'investment_plan_id' => $investment_plan->id,
                        'subscription_id' => $subscription_id,
                        'amount' => $amount,
                        'unit_number' => $request->unit_number ?? null,
                        'unit_price' => $request->housing_price ?? null,
                        'total_earning' => 0.00,
                    ]);
            
                    $cooling_period_date = $investmentSubscription->created_at->addDays(60)->startOfDay();
                    $next_roi_date = $cooling_period_date->copy()->addMonth()->startOfMonth();
                    $expired_date = $cooling_period_date->copy()->addMonths($investment_plan->investment_period)->endOfMonth();

                    $investmentSubscription->update([
                        'next_roi_date' => $next_roi_date,
                        'expired_date' => $expired_date,
                    ]);
                break;

                case('staking'):
                    $coin = Coin::find($request->from_coin_id);
                    $unit = $request->unit;
                    $stacking_fee = $request->stacking_fee;
        
                    $minAmount = $investment_plan->investment_min_amount;
        
                    if ($unit < $minAmount) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Unit minimum amount is ' . $minAmount . ' ' . $coin->setting_coin->name
                        ]);
                    }
        
                    if ($wallet->balance < $stacking_fee) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Insufficient MUSD Wallet Balance',
                            'title' => 'Insufficient MUSD Wallet Balance',
                            'warning' => 'MUSD wallet does not have enough balance to pay the staking fee. Please increase the wallet balance via internal transfer.',
                            'alertButton' => 'Internal Wallet',
                        ]);
                    }

                    $updatedUnit = $coin->unit - $unit;
                    $updatedBalance = $wallet->balance - $stacking_fee;
        
                    $coin->update(['unit' => $updatedUnit]);
                    $wallet->update(['balance' => $updatedBalance]);
        
                    $subscription_number = RunningNumberService::getID('investment');
                    $transaction_number = RunningNumberService::getID('transaction');

                    $asset_transaction = Transaction::create([
                        'category' => 'asset',
                        'user_id' => $user->id,
                        'transaction_type' => 'Staking',
                        'from_coin_id' => $coin->id,
                        'transaction_number' => $transaction_number,
                        'unit' => $unit,
                        'amount' => $amount,
                        'price_per_unit' => $request->price,
                        'transaction_charges' => $stacking_fee,
                        'transaction_amount' => $amount,
                        'status' => 'Success',
                        'remarks' => $unit . $coin->setting_coin->name . ' Unit + $' . $stacking_fee . ' from MUSD Wallet',
                        'new_wallet_amount' => $wallet->balance,
                        'new_coin_amount' => $coin->unit,
                    ]);
        
                    $wallet_transaction = Transaction::create([
                        'category' => 'wallet',
                        'user_id' => $user->id,
                        'transaction_type' => 'StakingFee',
                        'from_wallet_id' => $wallet->id,
                        'transaction_number' => RunningNumberService::getID('transaction'),
                        'amount' => $stacking_fee,
                        'transaction_charges' => 0,
                        'transaction_amount' => $stacking_fee,
                        'status' => 'Success',
                        'remarks' => $unit . $coin->setting_coin->name . ' Unit + $' . $stacking_fee . ' from MUSD Wallet',
                        'new_wallet_amount' => $wallet->balance,
                        'new_coin_amount' => $coin->unit,
                    ]);
        
                    $staking = CoinStacking::create([
                        'user_id' => $user->id,
                        'coin_id' => $coin->id,
                        'transaction_id' => $asset_transaction->id,
                        'investment_plan_id' => $investment_plan->id,
                        'subscription_number' => $subscription_number,
                        'stacking_unit' => $unit,
                        'stacking_price' => $amount,                            
                        'stacking_fee' => $stacking_fee,
                        'total_earning' => 0.00,
                    ]);

                    $next_roi_date = $staking->created_at->addMonth()->startOfMonth();
                    $expired_date = $staking->created_at->copy()->addDays($investment_plan->investment_period);
        
                    if ($staking->created_at->lt(now()->setTime(17, 0, 0))) {
                        // If created_at is before today at 5 PM
                        $autoAssignDate = now()->addDay()->startOfDay();
                    } else {
                        // If created_at is at or after today at 5 PM
                        $autoAssignDate = now()->addDays(2)->startOfDay();
                    }
    
                    $staking->update([
                        'next_roi_date' => $next_roi_date,
                        'expired_date' => $expired_date,
                        'max_capped_price' => $staking->stacking_price * 3,
                        'auto_assign_at' => $autoAssignDate
                    ]);

                    $upline = $user->upline;
                    $downline = $user;
    
                    if ($upline && $staking) {
                        if ($upline->binary) {
                            \Notification::send([$upline], new StakingNotification($upline, $downline));
                        }
                    }
    
                break;
                        
                default:
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'The selected investment plan is invalid.',
                        'title' => 'Invalid Investment Plan',
                        'warning' => 'Something went wrong on the selected plan',
                    ]);
                }
        
            return response()->json([
                'status' => 'success',
                'message' => 'The selected investment plan has been subscribed successfully.',
                'subscription' => $investmentSubscription,
                'transaction' => $transaction,
                'staking' => $staking,
            ]);
        }
    }

    public function earning_history()
    {
        $standardRewards = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'StandardRewards')
            ->where('category', 'standard')
            ->sum('after_amount');

        $standardReferralEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $affiliateEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'AffiliateEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $dividendEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'DividendEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $affiliateDividendEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'AffiliateDividendEarnings')
            ->where('category', 'standard')
            ->sum('after_amount');

        $stakingRewards = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'StakingRewards')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $stakingReferralEarnings= Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'ReferralEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $pairingEarnings= Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'PairingEarnings')
            ->where('category', 'staking')
            ->sum('after_coin_price');

        $investment_plans = InvestmentPlan::query()
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        $translatedInvestmentPlans = $investment_plans->map(function ($investmentPlan) {
            return [
                'id' => $investmentPlan->id,
                'name' => $investmentPlan->getTranslation('name', app()->getLocale()),
            ];
        });
        $translatedInvestmentPlans->prepend(['id' => '', 'name' => 'All']);
        
        $totalEarning = Earning::query()
            ->where('upline_id', \Auth::id())
            ->sum('after_coin_price');

        $totalWithdrawal = Transaction::where('user_id', \Auth::id())
            ->where('transaction_type', '=', 'Withdrawal')
            ->where('status', '=', 'Success')
            ->sum('transaction_amount');

        $standardInvestment = InvestmentSubscription::where('user_id', \Auth::id())
            ->whereNotIn('status', ['Terminated'])
            ->sum('amount');
        
        $stakingInvestment = CoinStacking::where('user_id', \Auth::id())
            ->whereNotIn('status', ['Terminated'])
            ->sum('stacking_price');
        
        $totalInvestment = $standardInvestment + $stakingInvestment;

        return response()->json([
            'standardRewards' => $standardRewards,
            'standardReferralEarnings' => $standardReferralEarnings,
            'affiliateEarnings' => $affiliateEarnings,
            'dividendEarnings' => $dividendEarnings,
            'affiliateDividendEarnings' => $affiliateDividendEarnings,
            'stakingRewards' => $stakingRewards,
            'stakingReferralEarnings' => $stakingReferralEarnings,
            'pairingEarnings' => $pairingEarnings,
            'investmentPlans' => $translatedInvestmentPlans,
            'totalEarning' => floatval($totalEarning),
            'totalWithdrawal' => floatval($totalWithdrawal),
            'totalInvestment' => floatval($totalInvestment),
        ]);
    }

    public function subscription_history()
    {
        $user = \Auth::user();
        $locale = app()->getLocale(); // Get the current locale

        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,investment_min_amount,roi_per_annum,investment_period,type')
            ->where('user_id', $user->id)
            ->get();

        $stakings = CoinStacking::query()
            ->with('investment_plan:id,name,investment_min_amount,investment_period,type')
            ->where('user_id', $user->id)
            ->get();

        $totalEarning = $stakings->sum('total_earning');
        $maxCap = $stakings->sum('max_capped_price');
    
        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => $investmentSubscription->investment_plan->getTranslation('name', $locale),
                'minimun_investment' => $investmentSubscription->investment_plan->investment_min_amount,
                'starting_date' => $investmentSubscription->created_at,
                'last_valid_date' => $investmentSubscription->expired_date,
                'subscription_id' => $investmentSubscription->subscription_id,
                'type' => $investmentSubscription->investment_plan->type,
                'amount' => $investmentSubscription->amount,
                'total_earning' => $investmentSubscription->total_earning,
                'status' => $investmentSubscription->status,
                'next_roi_date' => $investmentSubscription->next_roi_date,
            ];
        });

        $coinStakings = $stakings->map(function ($coinStaking) use ($locale) {
            return [
                'id' => $coinStaking->id,
                'plan_name' => $coinStaking->investment_plan->getTranslation('name', $locale),
                'minimun_investment' => $coinStaking->investment_plan->investment_min_amount,
                'investment_period' => $coinStaking->investment_plan->investment_period,
                'subscription_id' => $coinStaking->subscription_number,
                'type' => $coinStaking->investment_plan->type,
                'amount' => $coinStaking->stacking_unit,
                'link_price' => $coinStaking->stacking_price,
                'total_earning' => $coinStaking->total_earning,
                'status' => $coinStaking->status,
                'next_roi_date' => $coinStaking->next_roi_date,
                'expired_date' => $coinStaking->expired_date,
                'created_at' => $coinStaking->created_at,
                'max_capped_price' => $coinStaking->max_capped_price,
            ];
        });


        return response()->json([
            'subscriptions' => $investmentSubscriptions,
            'staking' => $coinStakings,
            'setting_coin' => SettingCoin::where('symbol', 'MXT/USD')->first(),
            'maxCap' => $maxCap,
            'totalEarning' => $totalEarning
        ]);
    }
}
