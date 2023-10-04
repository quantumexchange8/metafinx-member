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

        $code = 'VE-MB28-TXJ1R_2023-10-30';
        $serial_number = base64_encode($code);
        dd($serial_number);

        return Inertia::render('Earn/Earn', [
            'investmentPlans' => $investment_plans
        ]);
    }
}
