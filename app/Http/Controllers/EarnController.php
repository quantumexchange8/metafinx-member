<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvestmentSubscriptionRequest;
use App\Models\InvestmentPlan;
use App\Models\InvestmentSubscription;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EarnController extends Controller
{
    public function invest_subscription()
    {
        $wallets = Wallet::where('user_id', \Auth::id());

        $investment_plans = InvestmentPlan::query()
            ->with('descriptions:investment_plan_id,description')
            ->where('status', 'active')
            ->select('id', 'name', 'roi_per_annum')
            ->get();

        $wallet_sel = $wallets->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name . ' - $ ' . $wallet->balance,
            ];
        });

        $translatedInvestmentPlans = $investment_plans->map(function ($investmentPlan) {
            return [
                'id' => $investmentPlan->id,
                'name' => $investmentPlan->getTranslation('name', app()->getLocale()), // Change 'en' to your desired language code
                'roi_per_annum' => $investmentPlan->roi_per_annum,
                'descriptions' => $investmentPlan->descriptions->map(function ($description) {
                    return [
                        'description' => $description->getTranslation('description', app()->getLocale()), // Change 'en' to your desired language code
                    ];
                }),
            ];
        });

        return Inertia::render('Earn/Earn', [
            'investmentPlans' => $translatedInvestmentPlans,
            'wallet_sel' => $wallet_sel,
        ]);
    }

    public function subscribe(InvestmentSubscriptionRequest $request)
    {
        $user = \Auth::user();
        $investment_plan = InvestmentPlan::find($request->investment_plan_id);
        $wallet = Wallet::find($request->wallet_id);
        $amount = $request->amount;

        if ($amount < $investment_plan->investment_min_amount) {
            throw ValidationException::withMessages(['amount' => 'Amount minimum is $ ' . $investment_plan->investment_min_amount]);
        }

        if ($wallet->balance < $amount) {
            return redirect()->back()->with('title', 'Insufficient Balance')->with('warning', 'The selected wallet does not have enough balance to subscribe the investment plan. Please try again.');
        } else {
            $updated_balance = $wallet->balance - $amount;

            $wallet->update([
                'balance' => $updated_balance
            ]);
        }

        $subscription_id = RunningNumberService::getID('investment');

        $investmentSubscription = InvestmentSubscription::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'investment_plan_id' => $investment_plan->id,
            'subscription_id' => $subscription_id,
            'amount' => $amount,
            'total_earning' => 0.00,
        ]);

        $cooling_period_date = $investmentSubscription->created_at->addDays(60)->startOfDay();
        $next_roi_date = $cooling_period_date->copy()->addMonth()->startOfMonth();
        $expired_date = $cooling_period_date->copy()->addMonths($investment_plan->investment_period)->endOfMonth();

        $investmentSubscription->update([
            'next_roi_date' => $next_roi_date,
            'expired_date' => $expired_date,
        ]);

        return redirect()->back()->with('title', 'Subscribed!')->with('success', 'The selected investment plan has been subscribed successfully.');
    }

    public function investment()
    {
        $user = \Auth::user();

        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period')
            ->where('user_id', $user->id)
            ->get();

        $locale = app()->getLocale(); // Get the current locale

        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => [
                    'name' => $investmentSubscription->investment_plan->getTranslation('name', $locale),
                ],
                'roi_per_annum' => $investmentSubscription->investment_plan->roi_per_annum,
                'investment_period' => $investmentSubscription->investment_plan->investment_period,
                'subscription_id' => $investmentSubscription->subscription_id,
                'amount' => $investmentSubscription->amount,
                'total_earning' => $investmentSubscription->total_earning,
                'status' => $investmentSubscription->status,
                'next_roi_date' => $investmentSubscription->next_roi_date,
                'expired_date' => $investmentSubscription->expired_date,
                'created_at' => $investmentSubscription->created_at,
            ];
        });

        return Inertia::render('Earn/MyInvestment', [
            'investments' => $investmentSubscriptions
        ]);
    }
}
