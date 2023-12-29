<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\SettingWithdrawalFee;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Http;
use Illuminate\Http\Request;
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
            'payment_charges' => $request->payment_charges,
            'to_wallet_address' => $request->to_wallet_address,
            'price' => $request->amount,
            'status' => 'Pending'
        ]);

        $payout = config('payout-setting');
        $hashedToken = md5('metafinx@support.com' . $payout['apiKey']);
        $params = [
            "token" => $hashedToken,
            "transactionID" => $payment->transaction_id,
            "address" => $payment->to_wallet_address,
            "currency" => 'TRC20',
            "amount" => $payment->amount,
            "TxID" => $payment->txn_hash,
        ];

        $url = $payout['base_url'] . '/receiveDeposit';
        $response = Http::post($url, $params);
        \Log::debug($response);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.deposit_submit_success_message'));
    }

    public function withdrawal(WithdrawalRequest $request)
    {
        $user = \Auth::user();
        $amount = floatval($request->amount);
        $wallet = Wallet::find($request->wallet_id);
        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }
        $withdrawal_fee = SettingWithdrawalFee::latest()->first();
        $amount_with_fee = $amount + $withdrawal_fee->amount;
        $wallet->balance -= $amount_with_fee;
        $wallet->save();

        $transaction_id = RunningNumberService::getID('transaction');

        $payment = Payment::create([
            'user_id' => $user->id,
            'wallet_id' => $request->wallet_id,
            'transaction_id' => $transaction_id,
            'type' => 'Withdrawal',
            'amount' => $amount,
            'payment_charges' => $request->payment_charges,
            'to_wallet_address' => $request->wallet_address,
            'status' => 'Processing'
        ]);

        $payout = config('payout-setting');
        $hashedToken = md5('metafinx@support.com' . $payout['apiKey']);
        $params = [
            "token" => $hashedToken,
            "transactionID" => $payment->transaction_id,
            "address" => $payment->to_wallet_address,
            "currency" => 'TRC20',
            "amount" => $payment->amount,
            "payment_charges" => $payment->payment_charges,
        ];

        $url = $payout['base_url'] . '/receiveWithdrawal';
        $response = \Http::post($url, $params);

        return redirect()->back()->with('title', trans('public.submit_success'))->with('success', trans('public.withdrawal_submit_success_message'));
    }

    public function updateDeposit(Request $request)
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
            $wallet = Wallet::find($payment->wallet_id);
            if ($payment->status =='Success') {
                if ($payment->type == 'Deposit') {
                    $wallet->update([
                        'balance' => $wallet->balance + $payment->amount
                    ]);
                }
            } else {
                if ($payment->type == 'Withdrawal') {
                    $wallet->update([
                        'balance' => $wallet->balance + $payment->amount
                    ]);
                }

                PaymentStatus::create([
                    'message' => 'Payment with ID ' . $payment->id . ', STATUS is ' . $payment->status
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Deposit Success']);
    }
}
