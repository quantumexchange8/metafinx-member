<?php

namespace App\Http\Controllers;

use App\Models\InvestmentSubscription;
use App\Models\Wallet;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', \Auth::id());
        $investmentEarnings = InvestmentSubscription::where('user_id', \Auth::id());

        return Inertia::render('Dashboard', [
            'totalWalletBalance' => $wallets->sum('balance'),
            'walletLastUpdate' => $wallets->latest()->first('updated_at'),
            'investmentEarningsLastUpdate' => $investmentEarnings->latest()->first('updated_at'),
        ]);
    }
}
