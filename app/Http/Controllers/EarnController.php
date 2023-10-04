<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use Inertia\Inertia;

class EarnController extends Controller
{
    public function invest_subscription()
    {
        $investment_plans = InvestmentPlan::query()
            ->with('descriptions')
            ->where('status', 'active')
            ->select('id', 'name', 'roi_per_annum')
            ->get();

        $investment_plans->load('descriptions');

        return Inertia::render('Earn/Earn', [
            'investmentPlans' => $investment_plans
        ]);
    }
}
