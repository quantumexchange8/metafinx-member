<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\Payment;
use App\Models\Wallet;
use App\Services\RunningNumberService;

class DepositController extends Controller
{
    public function deposit(DepositRequest $request)
    {
        $user = \Auth::user();
        $transaction_id = RunningNumberService::getID('transaction');

        Payment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'transaction_id' => $transaction_id,
            'txn_hash' => $request->txn_hash,
            'type' => 'Deposit',
            'amount' => $request->amount,
            'price' => $request->amount,
            'status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'Submitted successfully');
    }
}
