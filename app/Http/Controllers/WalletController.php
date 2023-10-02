<?php

namespace App\Http\Controllers;

use App\Exports\DepositExport;
use App\Models\Payment;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class WalletController extends Controller
{
    public function details()
    {
        $wallets = Wallet::where('user_id', \Auth::id())->get();

        $wallet_sel = $wallets->map(function ($country) {
            return [
                'value' => $country->id,
                'label' => $country->name,
            ];
        });
        return Inertia::render('Wallet/Wallet', [
            'wallets' => $wallets,
            'wallet_sel' => $wallet_sel,
        ]);
    }

    public function getTransaction(Request $request)
    {
        $deposits = Payment::query()->with(['user', 'wallet'])
            ->where('type', 'Deposit')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('wallet', function ($wallet_query) use ($search) {
                    $wallet_query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%');
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $dateRange = explode(' - ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();

                $query->whereBetween('created_at', [$start_date, $end_date]);
            });

        if ($request->has('export')) {
            return Excel::download(new DepositExport($deposits), \Illuminate\Support\Carbon::now() . '-deposits-report.xlsx');
        }

        $deposit_results = $deposits->latest()
            ->paginate(5);

        return response()->json([
            'deposits' => $deposit_results,
        ]);
    }
}
