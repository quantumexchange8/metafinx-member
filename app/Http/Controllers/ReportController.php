<?php

namespace App\Http\Controllers;

use App\Exports\DepositExport;
use App\Exports\WithdrawalExport;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\SettingWalletAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use function Symfony\Component\Translation\t;

class ReportController extends Controller
{
    public function detail()
    {
        $wallets = Wallet::where('user_id', \Auth::id());

        $totalBalance = clone $wallets;

        $wallet_sel = $wallets->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name,
            ];
        });

        $wallet_address = SettingWalletAddress::inRandomOrder()->first();

        return Inertia::render('Report/Report', [
            'wallets' => $wallets->get(),
            'totalBalance' => $totalBalance->sum('balance'),
            'wallet_sel' => $wallet_sel,
            'random_address' => $wallet_address,
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