<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\InvestmentSubscription;
use App\Models\SettingCoin;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Coin;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\CoinPrice;
use App\Models\CoinPayment;
use Illuminate\Http\Request;
use App\Exports\DepositExport;
use App\Models\CoinMarketTime;
use App\Models\ConversionRate;
use App\Exports\WithdrawalExport;
use App\Exports\CoinPaymentExport;
use Illuminate\Support\Facades\DB;
use App\Models\SettingWalletAddress;
use App\Models\SettingWithdrawalFee;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\BuyCoinRequest;
use App\Services\RunningNumberService;
use function Symfony\Component\Translation\t;


class WalletController extends Controller
{
    public function details()
    {
        $wallets = Wallet::where('user_id', \Auth::id());

        $totalBalance = clone $wallets;

        $wallet_sel = $wallets->where('type', 'internal_wallet')->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name,
                'balance' => $wallet->balance,
            ];
        });

        $wallet_address = SettingWalletAddress::inRandomOrder()->first();

        $coins = Coin::with('setting_coin')->where('user_id', \Auth::id())->get();
        $coin_price = CoinPrice::whereDate('price_date', today())->first();
        $conversion_rate = ConversionRate::latest()->first();
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first();
        $coin_market_time = CoinMarketTime::whereIn('setting_coin_id', $coins->pluck('setting_coin_id'))->latest()->first();

        return Inertia::render('Wallet/Wallet', [
            'coins' => $coins,
            'coin_price' => $coin_price,
            'conversion_rate' => $conversion_rate,
            'coin_market_time' => $coin_market_time,
            'totalBalance' => $totalBalance->sum('balance'),
            'wallet_sel' => $wallet_sel,
            'random_address' => $wallet_address,
            'withdrawalFee' => SettingWithdrawalFee::latest()->first(),
            'setting_coin' => SettingCoin::where('symbol', 'XLC/MYR')->first(),
            'coin_price_yesterday' => $coin_price_yesterday,
        ]);
    }

    public function getWalletBalance(Request $request)
    {
        $wallets = Wallet::query()
            ->where('user_id', \Auth::id())
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->select('id', 'name', 'type', 'balance')
            ->get();

        $chartData = [
            'labels' => $wallets->pluck('name'),
            'datasets' => [],
        ];

        $backgroundColors = ['internal_wallet' => '#FF2D55', 'musd_wallet' => '#F79009'];

        foreach ($wallets as $wallet) {
            $dataset = [
                'label' => $wallet->name,
                'data' => [$wallet->balance],
                'backgroundColor' => $backgroundColors[$wallet->type],
                'borderColor' => '#384250',
                'borderWidth' => 4,
                'circumference' => [
                    $wallet->balance / $wallets->sum('balance') * 360
                ]
            ];

            $chartData['datasets'][] = $dataset;
        }

        return response()->json($chartData);
    }

    public function getTransaction(Request $request, $type)
    {
        $user = \Auth::user();

        if ($type === 'Deposit' || $type === 'Withdrawal') {
            $paymentQuery = Payment::query()->with(['user', 'wallet'])
                ->where('user_id', $user->id)
                ->where('type', $type);

            if ($request->filled('search')) {
                $search = '%' . $request->input('search') . '%';
                $paymentQuery->where(function ($q) use ($search) {
                    $q->whereHas('wallet', function ($walletQuery) use ($search) {
                        $walletQuery->where('name', 'like', $search);
                    })
                        ->orWhere('transaction_id', 'like', $search)
                        ->orWhere('amount', 'like', $search)
                        ->orWhere('price', 'like', $search);
                });
            }

            if ($request->filled('date')) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

                $paymentQuery->whereBetween('created_at', [$start_date, $end_date]);
            }

            if ($request->has('export')) {
                if ($type === 'Deposit') {
                    return Excel::download(new DepositExport($paymentQuery), Carbon::now() . '-' . $type . '-report.xlsx');
                } elseif ($type === 'Withdrawal') {
                    return Excel::download(new WithdrawalExport($paymentQuery), Carbon::now() . '-' . $type . '-report.xlsx');
                }
            }

            $paymentResults = $paymentQuery->latest()->paginate(10);

            return response()->json([$type => $paymentResults]);

        } elseif ($type === 'CoinPayment') {
            $coinPaymentQuery = CoinPayment::query()->with(['user', 'wallet'])->where('user_id', $user->id);

            if ($request->filled('search')) {
                $search = '%' . $request->input('search') . '%';
                $coinPaymentQuery->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', $search)
                        ->orWhere('amount', 'like', $search)
                        ->orWhere('price', 'like', $search)
                        ->orWhere('unit', 'like', $search);
                });
            }

            if ($request->filled('date')) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

                $coinPaymentQuery->whereBetween('created_at', [$start_date, $end_date]);
            }

            if ($request->has('export')) {
                return Excel::download(new CoinPaymentExport($coinPaymentQuery), Carbon::now() . '-' . $type . '-report.xlsx');
            }

            $coinPaymentResults = $coinPaymentQuery->latest()->paginate(10);

            return response()->json([$type => $coinPaymentResults]);
        }
    }

    public function getWalletHistory(Request $request, $wallet_id)
    {
        $user = \Auth::user();

        // Payments
        $payments = DB::table('users')
            ->select(
                'payments.id as payment_id',
                'payments.type as transaction_type',
                'payments.amount as transaction_amount',
                'payments.transaction_id as transaction_id',
                'payments.status as transaction_status',
                'payments.created_at as transaction_date'
            )
            ->leftJoin('payments', 'users.id', '=', 'payments.user_id')
            ->where('users.id', $user->id)
            ->where('payments.wallet_id', $wallet_id);

        // Earnings
        $earnings = DB::table('users')
            ->select(
                'earnings.id as earning_id',
                'earnings.type as transaction_type',
                'earnings.after_amount as transaction_amount',
                'earnings.percentage as transaction_id',
                'earnings.status as transaction_status',
                'earnings.roi_release_date as transaction_date'
            )
            ->leftJoin('earnings', 'users.id', '=', 'earnings.upline_id')
            ->where('users.id', $user->id)
            ->where('earnings.upline_wallet_id', $wallet_id);

        // Subscriptions
        $subscriptions = DB::table('users')
            ->select(
                'investment_subscriptions.id as subscription_id',
                'investment_subscriptions.type as transaction_type',
                'investment_subscriptions.amount as transaction_amount',
                'investment_subscriptions.subscription_id as transaction_id',
                'investment_subscriptions.status as transaction_status',
                'investment_subscriptions.created_at as transaction_date'
            )
            ->leftJoin('investment_subscriptions', 'users.id', '=', 'investment_subscriptions.user_id')
            ->where('users.id', $user->id)
            ->where('investment_subscriptions.wallet_id', $wallet_id);

        // Buy coins
        $buy_coins = DB::table('users')
            ->select(
                'coin_payments.id as coin_payment_id',
                'coin_payments.type as transaction_type',
                'coin_payments.amount as transaction_amount',
                'coin_payments.transaction_id as transaction_id',
                'coin_payments.status as transaction_status',
                'coin_payments.created_at as transaction_date'
            )
            ->leftJoin('coin_payments', 'users.id', '=', 'coin_payments.user_id')
            ->where('users.id', $user->id)
            ->where('coin_payments.wallet_id', $wallet_id);

        // Search condition
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';

            $payments->where('payments.transaction_id', 'like', $search);
            $earnings->where('earnings.type', 'like', $search);
            $subscriptions->where('investment_subscriptions.subscription_id', 'like', $search);
            $buy_coins->where('coin_payments.transaction_id', 'like', $search);
        }

        // Check for the date condition
        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            // Apply date range condition to each query
            $payments->whereBetween('payments.created_at', [$start_date, $end_date]);
            $earnings->whereBetween('earnings.roi_release_date', [$start_date, $end_date]);
            $subscriptions->whereBetween('investment_subscriptions.created_at', [$start_date, $end_date]);
            $buy_coins->whereBetween('coin_payments.created_at', [$start_date, $end_date]);
        }

        // Check for the type condition
        if ($request->filled('type')) {
            $type = $request->input('type');
            $payments->where('payments.type', $type);
            $earnings->where('earnings.type', $type);
            $subscriptions->where('investment_subscriptions.type', $type);
            $buy_coins->where('coin_payments.type', $type);
        }

        // Union the results
        $combinedResults = $payments->union($earnings)->union($subscriptions)->union($buy_coins);

        // Apply orderBy
        $combinedResults = $combinedResults->orderByDesc('transaction_date')->paginate(10);

        return response()->json($combinedResults);
    }

    public function getCoinChart(Request $request)
    {
        $coinPrices = CoinPrice::query()
            ->when($request->filled('month'), function ($query) use ($request) {
                $monthsAgo = now()->subMonths($request->input('month'));
                $query->where('price_date', '>=', $monthsAgo);
            })
            ->whereDate('price_date', '<=', now())
            ->select(
                DB::raw('DAY(price_date) as day'),
                DB::raw('SUM(price) as price')
            )
            ->groupBy('day')
            ->get();

        $coin_price = CoinPrice::whereDate('price_date', today())->first()->price;
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first()->price;

        $year = Carbon::now()->year;

        // Initialize the chart data structure
        $labels = [];

        if ($request->month == 1) {
            // If $request->month is 1, display the current month in the labels
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, Carbon::now()->month, $year);
            $month = Carbon::now()->month;
        } else {
            $month = Carbon::now()->month;
            // If $request->month is greater than 1, display the specified month in the labels
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = \DateTime::createFromFormat('j-n-Y', "$day-$month-$year");
            $labels[] = $date->format('j M');
        }


        $chartData = [
            'labels' => $labels,
            'datasets' => [],
        ];

        $borderColor = '#12b76a66';
        if ($coin_price > $coin_price_yesterday) {
            $borderColor = '#12B76A';
        } elseif ($coin_price < $coin_price_yesterday) {
            $borderColor = '#F04438';
        }

        // Loop through each unique type and create a dataset
        $dataset = [
            'data' => array_map(function ($label) use ($coinPrices) {
                // Extract day and month from the label (assuming the label is in 'd M' format)
                $parts = explode(' ', $label);
                $day = $parts[0];

                // Find the corresponding price in $coinPrices
                $priceData = $coinPrices->firstWhere('day', "$day");

                return $priceData ? $priceData->price : null;
            }, $chartData['labels']),
            'borderColor' => $borderColor,
            'borderWidth' => 2,
            'pointStyle' => false,
            'fill' => true
        ];

        $chartData['datasets'][] = $dataset;

        return response()->json($chartData);
    }

    public function buyCoin(BuyCoinRequest $request)
    {
        $user = \Auth::user();

        $transaction_id = RunningNumberService::getID('transaction');
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $request->setting_coin_id)->first();
        $total_unit = $coin->unit + $request->unit;
        $total_amount = $coin->amount + $request->amount;

        $wallet = Wallet::find($request->wallet_id);
        if ($wallet->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

        $wallet->decrement('balance', $request->amount);

        CoinPayment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'setting_coin_id' => $request->setting_coin_id,
            'transaction_id' => $transaction_id,
            'unit' => $request->unit,
            'price' => $request->price,
            'amount' => $request->amount,
            'conversion_rate' => $request->conversion_rate,
            'type' => 'BuyCoin',
            'status' => 'Success',
        ]);

        $coin->update([
            'unit' => $total_unit,
            'price' => $request->price,
            'amount' => $total_amount,
        ]);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.coin_purchase_success_message'));
    }

//    protected function chart_range()
//    {
//        $coinPrices = CoinPrice::query()
//            ->when($request->filled('month'), function ($query) use ($request) {
//                $monthsAgo = now()->subMonths($request->input('month'));
//                $query->where('price_date', '>=', $monthsAgo);
//            })
//            ->whereDate('price_date', '<=', now())
//            ->select(
//                DB::raw('DAY(price_date) as day'),
//                DB::raw('SUM(price) as price')
//            )
//            ->groupBy('day')
//            ->get();
//
//        $monthCoinPrices = CoinPrice::query()
//            ->when($request->filled('month'), function ($query) use ($request) {
//                $monthsAgo = now()->subMonths($request->input('month'));
//                $query->where('price_date', '>=', $monthsAgo);
//            })
//            ->whereDate('price_date', '<=', now())
//            ->select(
//                DB::raw('MONTH(price_date) as month'),
//                DB::raw('SUM(price) as price')
//            )
//            ->groupBy('month')
//            ->get();
//
//        $coin_price = CoinPrice::whereDate('price_date', today())->first()->price;
//        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first()->price;
//
//        $year = Carbon::now()->year;
//
//        // Initialize the chart data structure
//        $labels = [];
//
//        if ($request->filled('month') && $request->input('month') == 1) {
//            $month = Carbon::now()->month;
//            // If $request->month is 1, display the current month in days
//            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//
//            for ($day = 1; $day <= $daysInMonth; $day++) {
//                $date = \DateTime::createFromFormat('j-n-Y', "$day-$month-$year");
//                $labels[] = $date->format('j M');
//            }
//        } else {
//            // If $request->month is greater than 1, display the specified month in months
//            $shortMonthNames = [];
//            for ($month = 1; $month <= 12; $month++) {
//                $shortMonthNames[] = date('M', mktime(0, 0, 0, $month, 1));
//            }
//            $labels = $shortMonthNames;
//        }
//
//        $chartData = [
//            'labels' => $labels,
//            'datasets' => [],
//        ];
//
//        $borderColor = '#12b76a66';
//        if ($coin_price > $coin_price_yesterday) {
//            $borderColor = '#12B76A';
//        } elseif ($coin_price < $coin_price_yesterday) {
//            $borderColor = '#F04438';
//        }
//
//        // Loop through each unique type and create a dataset
//        $dataset = [
//            'data' => array_map(function ($label) use ($coinPrices) {
//                // Extract day and month from the label (assuming the label is in 'd M' format)
//                $parts = explode(' ', $label);
//                $day = $parts[0];
//
//                // Find the corresponding price in $coinPrices
//                $priceData = $coinPrices->firstWhere('day', "$day");
//
//                return $priceData ? $priceData->price : null;
//            }, $chartData['labels']),
//            'borderColor' => $borderColor,
//            'borderWidth' => 2,
//            'pointStyle' => false,
//            'fill' => true
//        ];
//
//        $datasetMonth = [
//            'data' => array_map(function ($month) use ($monthCoinPrices) {
//                return $monthCoinPrices->firstWhere('month', $month)->price ?? null;
//            }, range(1, 12)), // Use month numbers 1-12
//            'borderColor' => $borderColor,
//            'borderWidth' => 2,
//            'pointStyle' => false,
//            'fill' => true
//        ];
//
//        if ($request->filled('month') && $request->input('month') == 1) {
//            $chartData['datasets'][] = $dataset;
//        } else {
//            $chartData['datasets'][] = $datasetMonth;
//        }
//
//        return response()->json($chartData);
//    }

}
