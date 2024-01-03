<?php

namespace App\Http\Controllers;

use App\Exports\DepositExport;
use App\Exports\WithdrawalExport;
use App\Models\Coin;
use App\Models\CoinPrice;
use App\Models\ConversionRate;
use App\Models\Payment;
use App\Models\SettingWithdrawalFee;
use App\Models\Wallet;
use App\Models\SettingWalletAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
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

        return Inertia::render('Wallet/Wallet', [
            'coins' => $coins,
            'coin_price' => $coin_price,
            'conversion_rate' => $conversion_rate,
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

        $query = Payment::query()->with(['user', 'wallet'])->where('user_id', $user->id)->where('type', $type);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('wallet', function ($wallet_query) use ($search) {
                    $wallet_query->where('name', 'like', $search);
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

            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($request->has('export')) {
            if ($type == 'Deposit') {
                return Excel::download(new DepositExport($query), Carbon::now() . '-' . $type . '-report.xlsx');
            } elseif ($type == 'Withdrawal') {
                return Excel::download(new WithdrawalExport($query), Carbon::now() . '-' . $type . '-report.xlsx');
            }
        }

        $results = $query->latest()->paginate(10);

        return response()->json([$type => $results]);
    }
}
