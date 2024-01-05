<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Coin;
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
        ]);
    }

    public function getWalletBalance(Request $request)
    {
        $selectedPlans = Wallet::query()
            ->where('user_id', \Auth::id())
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->select('id', 'name', 'balance')
            ->get();

        $labels = $selectedPlans->pluck('name');
        $datasetData = $selectedPlans->pluck('balance');

        return response()->json([
            'labels' => $labels,
            'datasetData' => $datasetData,
        ]);
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

    public function buyCoin(BuyCoinRequest $request)
    {
        $user = \Auth::user();

        $validatedData = $request->validate([
            'terms' => 'required|accepted',
            'unit' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        $transaction_id = RunningNumberService::getID('transaction');
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $request->setting_coin_id)->first();
        $total_unit = $coin->unit + $request->unit;
        $total_amount = $coin->amount + $request->amount;

        CoinPayment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'setting_coin_id' => $request->setting_coin_id,
            'transaction_id' => $transaction_id,
            'unit' => $request->unit,
            'price' => $request->price,
            'amount' => $request->amount,
            'conversion_rate' => $request->conversion_rate,
            'type' => 'buy_coin',
            'status' => 'Success',
        ]);

        $coin->update([
            'unit' => $total_unit,
            'price' => $request->price,
            'amount' => $total_amount,
        ]);
        
        $wallet = Wallet::findOrFail($request->wallet_id);

        if ($wallet->balance < $request->amount) {
            return redirect()->back()->with('title', trans('public.insufficient_balance'))->with('warning', trans('public.insufficient_balance_warning'));
        }
        
        $wallet->decrement('balance', $request->amount);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.coin_purchase_success_message'));
    }

}
