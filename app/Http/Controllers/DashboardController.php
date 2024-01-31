<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coin;
use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Setting;
use App\Models\CoinPrice;
use App\Models\SettingCoin;
use Illuminate\Http\Request;
use App\Models\CoinMarketTime;
use App\Models\CoinStacking;
use App\Models\ConversionRate;
use App\Models\SettingWalletAddress;
use App\Models\InvestmentSubscription;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', \Auth::id());
//        $investmentEarnings = InvestmentSubscription::where('user_id', \Auth::id())->first('updated_at');
//
//        if ($investmentEarnings) {
//            $investmentEarningsLastUpdate = $investmentEarnings->updated_at;
//        } else {
//            $investmentEarningsLastUpdate = Carbon::now();
//        }
        $walletDeposits = clone $wallets;

        $wallet_sel = $walletDeposits->where('type', 'internal_wallet')->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name,
                'balance' => $wallet->balance,
            ];
        });

        $investmentEarning = Earning::where('upline_id', \Auth::id())
            ->sum('after_amount');

        $stakingEarning = CoinStacking::where('user_id', \Auth::id())
            ->sum('total_earning');
        
        $totalEarning = $investmentEarning + $stakingEarning;

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

        $coin = Coin::with('setting_coin')->where('user_id', \Auth::id())->first();
        $coin_price = CoinPrice::whereDate('price_date', today())->first() ?? CoinPrice::latest('price_date')->first();
        $conversion_rate = ConversionRate::latest()->first();
        $coin_market_time = CoinMarketTime::where('setting_coin_id', $coin->setting_coin_id)->latest()->first();
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first();

        $wallet_address = SettingWalletAddress::inRandomOrder()->first();

        return Inertia::render('Dashboard', [
            'wallets' => $wallets->get(),
            'wallet_sel' => $wallet_sel,
            'random_address' => $wallet_address,
            'coin' => $coin,
            'coin_price' => $coin_price,
            'setting_coin' => SettingCoin::where('symbol', 'MXT/USD')->first(),
            'conversion_rate' => $conversion_rate,
            'coin_market_time' => $coin_market_time,
            'coin_price_yesterday' => $coin_price_yesterday,
            'gasFee' => Setting::where('slug', 'gas-fee')->latest()->first(),
            'totalEarning' => floatval($totalEarning),
            'totalWithdrawal' => floatval($totalWithdrawal),
            'totalInvestment' => floatval($totalInvestment),
        ]);
    }

    public function markAsRead(Request $request)
    {
        $user = \Auth::user();
        $notifications = $user->unreadNotifications;

        foreach ($notifications as $notification) {
            if ($notification->id == $request->id && $notification->read_at == null) {
                $notification->markAsRead();
            }
        }

        return redirect()->back();
    }
}
