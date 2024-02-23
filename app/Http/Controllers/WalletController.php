<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coin;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\CoinPrice;
use App\Models\CoinPayment;
use App\Models\SettingCoin;
use App\Models\Transaction;
use App\Models\CoinStacking;
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
use App\Http\Requests\SwapCoinRequest;
use App\Models\InvestmentSubscription;
use App\Services\RunningNumberService;
use function Symfony\Component\Translation\t;
use App\Http\Requests\InternalTransferRequest;
use Illuminate\Validation\ValidationException;


class WalletController extends Controller
{
    public function details()
    {
        $wallets = Wallet::where('user_id', \Auth::id());

        $totalBalance = clone $wallets;
        $deposit_wallets = clone $wallets;

        // $wallet_sel = $wallets->get()->map(function ($wallet) {
        //     return [
        //         'value' => $wallet->id,
        //         'label' => $wallet->name,
        //         'balance' => $wallet->balance,
        //     ];
        // });

        $wallet_sel = $deposit_wallets->where('type', 'internal_wallet')->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name,
                'balance' => $wallet->balance,
            ];
        });

        $wallet_address = SettingWalletAddress::inRandomOrder()->first();

        $coins = Coin::with('setting_coin')->where('user_id', \Auth::id());

        $coinTotalPrice = clone $coins;

        $today = Carbon::today();
        $coin_price = CoinPrice::whereDate('price_date', $today)->first();

        // If today's coin price is null, try fetching yesterday's coin price
        if (!$coin_price) {
            $coin_price = CoinPrice::latest()->first();
        }

        $conversion_rate = ConversionRate::latest()->first();
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first();
        $coin_market_time = CoinMarketTime::whereIn('setting_coin_id', $coins->pluck('setting_coin_id'))->latest()->first();
        $coin_payment = CoinPayment::where('user_id', \Auth::id())->get();

        return Inertia::render('Wallet/Wallet', [
            'wallets' => $wallets->get(),
            'coins' => $coins->get(),
            'coin_price' => $coin_price,
            'conversion_rate' => $conversion_rate,
            'coin_market_time' => $coin_market_time,
            'totalBalance' => number_format($totalBalance->sum('balance') + ($coinTotalPrice->sum('unit') * $coin_price->price), 2),
            'wallet_sel' => $wallet_sel,
            // 'depositWalletSel' => $depositWalletSel,
            'random_address' => $wallet_address,
            'withdrawalFee' => Setting::where('slug', 'withdrawal-fee')->latest()->first(),
            'depositFee' => Setting::where('slug', 'deposit-fee')->latest()->first(),
            'gasFee' => Setting::where('slug', 'gas-fee')->latest()->first(),
            'stackingFee' => Setting::where('slug', 'stacking-fee')->latest()->first(),
            'setting_coin' => SettingCoin::where('symbol', 'MXT/USD')->first(),
            'coin_price_yesterday' => $coin_price_yesterday,
            'coin_payment' => $coin_payment,
        ]);
    }

    public function fetchWallets()
    {
        $wallets = Wallet::where('user_id', \Auth::id())->get();

        return response()->json($wallets);
    }

    public function getWalletBalance(Request $request)
    {
        $user = \Auth::user();
        $today = Carbon::today();
        $price_per_unit = CoinPrice::whereDate('price_date', $today)->first();

        // If today's coin price is null, try fetching yesterday's coin price
        if (!$price_per_unit) {
            $price_per_unit = CoinPrice::latest()->first();
        }

        $wallets = Wallet::query()
            ->where('user_id', $user->id)
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->select('id', 'name', 'type', 'balance')
            ->get();

        $coins = Coin::where('user_id', $user->id)
            ->with('setting_coin:id,name')
            ->select('id', 'user_id', 'setting_coin_id', 'unit')
            ->get();

        $coinData = $coins->map(function ($coin) {
            return [
                'name' => $coin->setting_coin->name,
                'price' => $coin->unit,
            ];
        });

        $coinNames = $coinData->pluck('name');
        $coinPrices = $coinData->pluck('price');

        $chartData = [
            'labels' => $wallets->pluck('name')->merge($coinNames),
            'datasets' => [],
        ];

        $backgroundColors = ['internal_wallet' => '#FF2D55', 'musd_wallet' => '#F79009', 'MXT' => '#EF5572'];
        $balances = [];
        $backgroundColor = [];

        foreach ($wallets as $wallet) {
            $balances[] = $wallet->balance;
            $backgroundColor[] = $backgroundColors[$wallet->type];
        }

        foreach ($coinNames as $coinName) {
            $backgroundColor[] = $backgroundColors[$coinName];
        }

        foreach ($coinPrices as $coinPrice) {
            $balances[] = number_format($coinPrice * $price_per_unit->price, 2, '.', '');
        }

        $dataset = [
            'data' => $balances,
            'backgroundColor' => $backgroundColor,
            'offset' => 5,
            'borderColor' => 'transparent'
        ];

        $chartData['datasets'][] = $dataset;


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

        // Earnings
        $earnings = DB::table('users')
            ->select(
                'earnings.id as earning_id',
                'earnings.type as transaction_type',
                'earnings.after_amount as transaction_amount',
                'earnings.percentage as transaction_id',
                'earnings.status as transaction_status',
                'earnings.remarks as transaction_remark',
                'earnings.roi_release_date as transaction_date'
            )
            ->leftJoin('earnings', 'users.id', '=', 'earnings.upline_id')
            ->when($request->search, function (Builder $query, string $search) {
                $query->where('earnings.type', 'like','%' . $search . '%');
            })
            ->when($request->date, function (Builder $query, $date) {
                $dateRange = explode(' - ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

                // Apply date range condition to each query
                $query->whereBetween('earnings.roi_release_date', [$start_date, $end_date]);
            })
            ->when($request->type, function (Builder $query, string $type) {
                $query->where('earnings.type', $type);
            })
            ->where('users.id', $user->id)
            ->where('earnings.upline_wallet_id', $wallet_id);

        $transactions = DB::table('users')
            ->select(
                'transactions.id as transaction_id',
                'transactions.transaction_type as transaction_type',
                'transactions.transaction_amount as transaction_amount',
                'transactions.transaction_number as transaction_id',
                'transactions.status as transaction_status',
                'transactions.remarks as transaction_remark',
                'transactions.created_at as transaction_date'
            )
            ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->when($request->search, function (Builder $query, string $search) {
                $query->where('transactions.transaction_number', 'like','%' . $search . '%');
            })
            ->when($request->date, function (Builder $query, $date) {
                $dateRange = explode(' - ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

                // Apply date range condition to each query
                $query->whereBetween('transactions.created_at', [$start_date, $end_date]);
            })
            ->when($request->type, function (Builder $query, string $type) {
                $query->where('transactions.transaction_type', $type);
            })
            ->where('users.id', $user->id)
            ->where('transactions.to_wallet_id', $wallet_id)
            ->orWhere('transactions.from_wallet_id', $wallet_id);

        // Search condition
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';

            $earnings->where('earnings.type', 'like', $search);
            $transactions->where('transactions.transaction_number', 'like', $search);
        }

        // Check for the date condition
        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            // Apply date range condition to each query
            $earnings->whereBetween('earnings.roi_release_date', [$start_date, $end_date]);
            $transactions->whereBetween('transactions.created_at', [$start_date, $end_date]);
        }

        // Check for the type condition
        if ($request->filled('type')) {
            $type = $request->input('type');
            $earnings->where('earnings.type', $type);
            $transactions->where('transactions.transaction_type', $type);
        }

        // Union the results
        $combinedResults = $earnings->union($transactions);

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
            DB::raw('DATE(price_date) as date'),
            DB::raw('SUM(price) as price')
        )
        ->groupBy('date')
        ->get();

        $labels = [];
        $data = [];

        foreach ($coinPrices as $priceData) {
            // Format date as 'j M'
            $formattedDate = Carbon::parse($priceData->date)->format('j M');

            $labels[] = $formattedDate;
            $data[] = $priceData->price;
        }
        $coin_price = CoinPrice::whereDate('price_date', today())->first()->price ?? CoinPrice::latest('price_date')->first()->price;
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first()->price;

        $borderColor = '#12b76a66';
        if ($coin_price > $coin_price_yesterday) {
            $borderColor = '#12B76A';
        } elseif ($coin_price < $coin_price_yesterday) {
            $borderColor = '#F04438';
        } else {
            $borderColor = '#9DA4AE';
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'pointStyle' => false,
                    'fill' => true,
                ],
            ],
        ];

        return response()->json($chartData);
    }

    public function internalTransfer(InternalTransferRequest $request)
    {
        $user = \Auth::user();
        $from_wallet = Wallet::find($request->from_wallet_id);
        $to_wallet = Wallet::find($request->to_wallet_id);

        if ($from_wallet->id == $to_wallet->id) {
            throw ValidationException::withMessages(['from_wallet_id' => 'Wallet cannot be the same']);
        }

        if ($from_wallet->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

        $transaction_number = RunningNumberService::getID('transaction');

        $transaction = Transaction::create([
            'category' => 'wallet',
            'user_id' => $user->id,
            'transaction_type' => 'InternalTransfer',
            'from_wallet_id' => $from_wallet->id,
            'to_wallet_id' => $to_wallet->id,
            'transaction_number' => $transaction_number,
            'amount' => $request->amount,
            'transaction_charges' => 0,
            'transaction_amount' => $request->amount,
            'status' => 'Success',
            'remarks' => $request->remarks,
            'new_wallet_amount' => $to_wallet->balance,
        ]);

        // Update the wallet balance
        $from_wallet->update([
            'balance' => $from_wallet->balance - $transaction->transaction_amount,
        ]);

        $to_wallet->update([
            'balance' => $to_wallet->balance + $transaction->transaction_amount,
        ]);

        $transaction->update([
            'new_wallet_amount' => $to_wallet->balance,
        ]);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.success_internal_transfer'));
    }

    public function buyCoin(BuyCoinRequest $request)
    {
        $user = \Auth::user();

        $transaction_id = RunningNumberService::getID('transaction');
        $setting_coin = SettingCoin::find($request->setting_coin_id);
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
        $total_unit = $coin->unit + $request->unit;

        $wallet = Wallet::find($request->wallet_id);
        if ($wallet->balance < $request->transaction_amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance') . ', PAYABLE AMOUNT: $' . $request->transaction_amount ]);
        }

        $wallet->decrement('balance', $request->transaction_amount);

        $transaction = Transaction::create([
            'category' => 'asset',
            'user_id' => $user->id,
            'transaction_type' => 'BuyCoin',
            'from_wallet_id' => $request->wallet_id,
            'to_coin_id' => $request->coin_id,
            'transaction_number' => $transaction_id,
            'unit' => $request->unit,
            'price_per_unit' => $request->price,
            'amount' => $request->amount,
            'transaction_charges' => $request->gas_fee,
            'transaction_amount' => $request->transaction_amount,
            'status' => 'Success',
            'new_wallet_amount' => $wallet->balance,
            'new_coin_amount' => $total_unit,
        ]);

        $coin->update([
            'unit' => $total_unit,
            'price' => $transaction->price_per_unit,
        ]);

        $accumulate_supply = $setting_coin->accumulate_supply + $transaction->unit;
        $accumulate_capped = $setting_coin->accumulate_capped + ($accumulate_supply * 2);

        $setting_coin->update([
            'accumulate_supply' => $accumulate_supply,
            'accumulate_capped' => $accumulate_capped,
        ]);

        $total_amount = $coin->unit / $coin->price;

        $coin->update([
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

    public function getCoinPaymentHistory(Request $request)
    {
        $user = \Auth::user();

        $buy_coin_history = Transaction::query()
            ->with('fromWallet:id,name', 'toWallet:id,name','coinStacking.investment_plan:id,name')
            ->where('user_id', $user->id)
            ->where('category', 'asset');

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';

            $buy_coin_history->where(function ($query) use ($search) {
                $query->where('transaction_type', 'like', $search)
                    ->orWhere('transaction_number', 'like', $search)
                    ->orWhere('unit', 'like', $search)
                    ->orWhere('transaction_amount', 'like', $search);
            });
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $dateRange = explode(' - ', $date);
            $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

            $buy_coin_history->whereBetween('created_at', [$start_date, $end_date]);
        }

        $buy_coin_history = $buy_coin_history->latest()->paginate(10);

        return response()->json($buy_coin_history);
    }

    public function swapCoin(BuyCoinRequest $request)
    {
        $user = \Auth::user();
        $transaction_id = RunningNumberService::getID('transaction');
        $setting_coin = SettingCoin::find($request->setting_coin_id);
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
        $total_unit = $coin->unit - $request->unit;

        $wallet = Wallet::find($request->wallet_id);

        if ($coin->unit < $request->unit) {
            throw ValidationException::withMessages(['unit' => trans('public.insufficient_unit') . ', ' . trans('public.available_unit') . ': ' . $coin->unit . ' ' . $setting_coin->name ]);

        }

        $wallet->increment('balance', $request->transaction_amount);

        $transaction = Transaction::create([
            'category' => 'asset',
            'user_id' => $user->id,
            'transaction_type' => 'SwapCoin',
            'to_wallet_id' => $request->wallet_id,
            'from_coin_id' => $request->coin_id,
            'transaction_number' => $transaction_id,
            'unit' => $request->unit,
            'price_per_unit' => $request->price,
            'amount' => $request->amount,
            'transaction_charges' => $request->gas_fee,
            'transaction_amount' => $request->transaction_amount,
            'status' => 'Success',
            'new_wallet_amount' => $wallet->balance,
            'new_coin_amount' => $total_unit,
        ]);

        $coin->update([
            'unit' => $total_unit,
            'price' => $transaction->price_per_unit,
        ]);

        $total_amount = $coin->unit / $coin->price;

        $coin->update([
            'amount' => $total_amount,
        ]);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.swap_coin_success_message'));
    }

}
