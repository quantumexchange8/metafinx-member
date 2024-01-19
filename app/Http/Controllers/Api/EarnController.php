<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\InvestmentPlan;
use App\Http\Controllers\Controller;
use App\Models\InvestmentSubscription;
use App\Services\RunningNumberService;
use Illuminate\Validation\ValidationException;

class EarnController extends Controller
{
    public function investment_plans()
    {
        $investment_plans = InvestmentPlan::query()
            ->with('descriptions:investment_plan_id,description')
            ->where('status', 'active')
            ->select('id', 'name', 'roi_per_annum')
            ->get();

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

        return response()->json($translatedInvestmentPlans);
    }

    public function my_investments()
    {
        $user = \Auth::user();

        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period,type')
            ->where('user_id', $user->id)
            ->get();

        $locale = app()->getLocale(); // Get the current locale

        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            $total_month = $investmentSubscription->investment_plan->investment_period;
            $created_at = Carbon::parse($investmentSubscription->created_at);

            $remaining_months = max(0, $total_month - $created_at->diffInMonths(Carbon::now()));

            $current_month = $total_month - $remaining_months;

            return [
                'id' => $investmentSubscription->id,
                'plan_name' => $investmentSubscription->investment_plan->getTranslation('name', $locale),
                'roi_per_annum' => $investmentSubscription->investment_plan->roi_per_annum,
                'total_month' => $total_month,
                'current_month' => $current_month,
                'id_number' => $investmentSubscription->subscription_id,
                'amount' => $investmentSubscription->amount,
                'type' => $investmentSubscription->investment_plan->type,
                'total_earning' => $investmentSubscription->total_earning,
                'situation' => $investmentSubscription->status,
                'next_roi_date' => $investmentSubscription->next_roi_date,
                'last_valid_date' => $investmentSubscription->expired_date,
                'starting_date' => $investmentSubscription->created_at,
            ];
        });

        return response()->json($investmentSubscriptions);
    }

    public function subscribe(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'integer'],
            'unit_number' => ['sometimes', 'required'],
            'housing_price' => ['sometimes', 'required', 'numeric', 'integer'],
            'terms' => ['accepted'],
        ])->setAttributeNames([
            'wallet_id' => 'Wallet',
            'amount' => 'Amount',
            'unit_number' => 'Unit Number',
            'housing_price' => 'Housing Price',
            'terms' => 'Terms & Conditions',
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

            return response()->json([
                'status' => 'success',
                'message' => 'The selected investment plan has been subscribed successfully.',
                'subscription' => $investmentSubscription,
                'transaction' => $transaction,
            ]);
        }
    }

    public function earning_history()
    {
        $user = \Auth::user();

        $earnings = Earning::where('upline_id', $user->id)
            ->select('id', 'upline_id', 'after_amount', 'type', 'created_at')
            ->get();

        return response()->json(['earnings' => $earnings]);
    }

    public function subscription_history()
    {
        $user = \Auth::user();

        $locale = app()->getLocale(); // Get the current locale

        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period')
            ->where('user_id', $user->id)
            ->get();

        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => [
                    'name' => $investmentSubscription->investment_plan->getTranslation('name', 'en'),
                ],
                'subscription_id' => $investmentSubscription->subscription_id,
                'amount' => $investmentSubscription->amount,
                'created_at' => $investmentSubscription->created_at,
            ];
        });

        return response()->json(['subscriptions' => $investmentSubscriptions]);
    }
}
