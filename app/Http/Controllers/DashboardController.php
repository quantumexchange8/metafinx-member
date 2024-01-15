<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\CoinMarketTime;
use App\Models\CoinPrice;
use App\Models\ConversionRate;
use App\Models\Earning;
use App\Models\InvestmentSubscription;
use App\Models\SettingCoin;
use App\Models\SettingWalletAddress;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        $referralEarnings = Earning::query()
            ->where('upline_id', \Auth::id())
            ->where('type', 'referral_earnings')
            ->sum('after_amount');

        $wallet_sel = $walletDeposits->where('type', 'internal_wallet')->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name,
                'balance' => $wallet->balance,
            ];
        });
        $coin = Coin::with('setting_coin')->where('user_id', \Auth::id())->first();
        $coin_price = CoinPrice::whereDate('price_date', today())->first();
        $conversion_rate = ConversionRate::latest()->first();
        $coin_market_time = CoinMarketTime::where('setting_coin_id', $coin->setting_coin_id)->latest()->first();
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first();

        $wallet_address = SettingWalletAddress::inRandomOrder()->first();

        return Inertia::render('Dashboard', [
            'wallets' => $wallets->get(),
            'referralEarnings' => $referralEarnings,
            'wallet_sel' => $wallet_sel,
            'random_address' => $wallet_address,
            'coin' => $coin,
            'coin_price' => $coin_price,
            'setting_coin' => SettingCoin::where('symbol', 'MXT/USD')->first(),
            'conversion_rate' => $conversion_rate,
            'coin_market_time' => $coin_market_time,
            'coin_price_yesterday' => $coin_price_yesterday,
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
