<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function deposit(DepositRequest $request)
    {
        $user = \Auth::user();
        $transaction_id = RunningNumberService::getID('transaction');

        $payment = Payment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'transaction_id' => $transaction_id,
            'txn_hash' => $request->txn_hash,
            'type' => 'Deposit',
            'amount' => $request->amount,
            'to_wallet_address' => $request->to_wallet_address,
            'price' => $request->amount,
            'status' => 'Pending'
        ]);

        $hashedToken = md5('MetaFinXmetafinx@support.com');

        return redirect()->back()->with('title', 'Submitted successfully')->with('success', 'The deposit request has been submitted successfully.');
    }

    public function withdrawal(WithdrawalRequest $request)
    {
        $user = \Auth::user();
        $amount = floatval($request->amount);
        $wallet = Wallet::find($request->wallet_id);
        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }
        $wallet->balance -= $amount;
        $wallet->save();

        $transaction_id = RunningNumberService::getID('transaction');

        Payment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'transaction_id' => $transaction_id,
            'type' => 'Withdrawal',
            'amount' => $amount,
            'to_wallet_address' => $request->wallet_address,
            'status' => 'Processing'
        ]);

        return redirect()->back()->with('title', 'Submitted successfully')->with('success', 'The withdrawal request has been submitted successfully.');
    }

    public function updateDeposit(DepositRequest $request)
    {
        $data = $request->all();

        \Log::debug($data);
        $result = [
            "token" => $data['token'],
            "transactionID" => $data['transactionID'],
            "address" => $data["address"],
            "amount" => $data["amount"],
            "status" => $data["status"],
            "remarks" => $data["remarks"],
        ];

        $payment = Payment::query()
            ->where('transaction_id', $result['transactionID'])
            ->first();

        $dataToHash = md5($payment->transaction_id . $payment->to_wallet_address);

        if ($result['token'] === $dataToHash) {
            //proceed approval
            $payment->update([
                'status' => $result['status'],
                'remarks' => $result['remarks']
            ]);

            if ($payment->status =='Success') {
                $wallet = Wallet::find($payment->wallet_id);

                $wallet->update([
                    'balance' => $wallet->balance + $payment->amount
                ]);
            } else {
                PaymentStatus::create([
                    'message' => 'Payment with ID ' . $payment->id . ', STATUS is ' . $payment->status
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Deposit Success']);
    }
}
