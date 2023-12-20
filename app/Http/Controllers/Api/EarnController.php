<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\InvestmentSubscription;
use Carbon\Carbon;

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
            ->with('investment_plan:id,name,roi_per_annum,investment_period')
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
                'type' => $investmentSubscription->type,
                'total_earning' => $investmentSubscription->total_earning,
                'situation' => $investmentSubscription->status,
                'next_roi_date' => $investmentSubscription->next_roi_date,
                'last_valid_date' => $investmentSubscription->expired_date,
                'starting_date' => $investmentSubscription->created_at,
            ];
        });

        return response()->json($investmentSubscriptions);
    }
}
