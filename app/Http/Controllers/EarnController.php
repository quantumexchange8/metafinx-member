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

        return Inertia::render('Earn/Earn', [
            'investmentPlans' => $investment_plans,
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
            'to_wallet_address' => $request->wallet_address,
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
            ->with('investment_plan:id,name,investment_period')
            ->where('user_id', $user->id)
            ->get();

        return Inertia::render('Earn/MyInvestment', [
            'investments' => $investments
        ]);
    }
}
