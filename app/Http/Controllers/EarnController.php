<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\CoinPrice;
use App\Models\SettingCoin;
use App\Models\Transaction;
use App\Models\CoinStacking;
use Illuminate\Http\Request;
use App\Models\InvestmentPlan;
use App\Models\InvestmentSubscription;
use App\Services\RunningNumberService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\InvestmentSubscriptionRequest;

class EarnController extends Controller
{
    public function invest_subscription()
    {
        $wallets = Wallet::where('user_id', \Auth::id());

        $investment_plans = InvestmentPlan::query()
            ->with('descriptions:investment_plan_id,description')
            ->where('status', 'active')
            ->select('id', 'name', 'roi_per_annum', 'type', 'commision_multiplier')
            ->get();

        $groupedInvestmentPlans = $investment_plans->groupBy('type');

        $wallet_sel = $wallets->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name . ' - $ ' . $wallet->balance,
            ];
        });

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
                    ];
                }),
            ];
        });

        return Inertia::render('Earn/Earn', [
            'investmentPlans' => $translatedInvestmentPlans,
            'wallet_sel' => $wallet_sel,
            'coin_price' => CoinPrice::whereDate('price_date', today())->first(),
            'musd_wallet' => Wallet::where('user_id', \Auth::id())->where('type', 'musd_wallet')->first(),
            'stackingFee' => Setting::where('slug', 'stacking-fee')->latest()->first(),
        ]);
    }

    public function subscribe(InvestmentSubscriptionRequest $request)
    {
        $user = \Auth::user();
        $investment_plan = InvestmentPlan::find($request->investment_plan_id);
        $wallet = Wallet::find($request->wallet_id);
        $setting_coin = SettingCoin::find($request->setting_coin_id);
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
        $amount = $request->amount;

        switch($investment_plan->type) {
            case('standard'):

                if ($amount % 100 !== 0) {
                    throw ValidationException::withMessages(['amount' => 'Please enter an amount in increments of 100.']);
                }

                if ($amount < $investment_plan->investment_min_amount) {
                    throw ValidationException::withMessages(['amount' => 'Amount minimum is $ ' . $investment_plan->investment_min_amount]);
                }

                if ($wallet->balance < $amount) {
                    return redirect()->back()->with('title', trans('public.insufficient_balance'))->with('warning', trans('public.insufficient_balance_warning'));
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
                    'new_coin_amount' => $coin->unit,        
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

            case('stacking'):
                $coin = Coin::find($request->from_coin_id);
                $unit = $request->unit;
                $stacking_fee = $request->stacking_fee;

                $minAmount = $investment_plan->investment_min_amount;

                if ($unit < $minAmount) {
                    throw ValidationException::withMessages(['unit' => 'Unit minimum amount is ' . $minAmount . ' ' . $coin->setting_coin->name]);
                }

                if ($wallet->balance < $stacking_fee) {
                    return redirect()->back()
                        ->with('title', 'Insufficient MUSD Wallet Balance')
                        ->with('warning', 'MUSD wallet does not have enough balance to pay the stacking fee. Please increase the wallet balance via internal transfer.')
                        ->with('alertButton', 'Internal Wallet');
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
                    'transaction_type' => 'Stacking',
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
                    'transaction_type' => 'StackingFee',
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

                $stacking = CoinStacking::create([
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

                $next_roi_date = $stacking->created_at->addMonth()->startOfMonth();
                $expired_date = $stacking->created_at->copy()->addDays($investment_plan->investment_period);

                $stacking->update([
                    'next_roi_date' => $next_roi_date,
                    'expired_date' => $expired_date,
                    'max_capped_price' => $stacking->stacking_price * 3
                ]);

                break;

            default:
                return redirect()->back()
                    ->with('title', 'Invalid Investment Plan')
                    ->with('warning', 'Something went wrong on the selected plan');
        }

        return redirect()->back()->with('title', trans('public.subscribed'))->with('success', trans('public.subscribed_success'));
    }

    public function investment()
    {
        $user = \Auth::user();

        // Get InvestmentSubscriptions
        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period,type')
            ->where('user_id', $user->id)
            ->whereIn('status', ['CoolingPeriod', 'OnGoingPeriod'])
            ->get();

        // Get CoinStackings
        $stackings = CoinStacking::query()
            ->with('investment_plan:id,name,investment_period,type')
            ->where('user_id', $user->id)
            ->where('status', 'OnGoingPeriod')
            ->get();

        $locale = app()->getLocale(); // Get the current locale

        // Map InvestmentSubscriptions
        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => [
                    'name' => $investmentSubscription->investment_plan->getTranslation('name', $locale),
                ],
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
                'plan_name' => [
                    'name' => $coinStacking->investment_plan->getTranslation('name', $locale),
                ],
                'investment_period' => $coinStacking->investment_plan->investment_period,
                'subscription_id' => $coinStacking->subscription_number,
                'type' => $coinStacking->investment_plan->type,
                'amount' => $coinStacking->stacking_unit,
                'total_earning' => $coinStacking->total_earning,
                'status' => $coinStacking->status,
                'next_roi_date' => $coinStacking->next_roi_date,
                'expired_date' => $coinStacking->expired_date,
                'created_at' => $coinStacking->created_at,
            ];
        });

        return Inertia::render('Earn/MyInvestment', [
            'investments' => $investmentSubscriptions,
            'coinStackings' => $coinStackings,
            'setting_coin' => SettingCoin::where('symbol', 'MXT/USD')->first()
        ]);
    }
}
