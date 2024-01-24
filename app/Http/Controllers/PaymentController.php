<?php

namespace App\Http\Controllers;

use Http;
use App\Models\Coin;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SettingCoin;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\SettingWithdrawalFee;
use App\Http\Requests\DepositRequest;
use App\Services\RunningNumberService;
use App\Http\Requests\WithdrawalRequest;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function deposit(DepositRequest $request)
    {
        $user = \Auth::user();
        $setting_coin = SettingCoin::find($request->setting_coin_id);
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
        $wallet = Wallet::find($request->wallet_id);
        $transaction_id = RunningNumberService::getID('transaction');

        $transaction = Transaction::create([
            'category' => 'wallet',
            'user_id' => $user->id,
            'to_wallet_id' => $request->wallet_id,
            'transaction_number' => $transaction_id,
            'txn_hash' => $request->txn_hash,
            'transaction_type' => 'Deposit',
            'amount' => $request->amount,
            'transaction_charges' => 0,
            'transaction_amount' => $request->amount,
            'to_wallet_address' => $request->to_wallet_address,
            'status' => 'Pending',
            'new_wallet_amount' => $wallet->balance,
            'new_coin_amount' => $coin->unit,
        ]);

        $payout = config('payout-setting');
        $hashedToken = md5('metafinx@support.com' . $payout['apiKey']);
        $params = [
            "token" => $hashedToken,
            "transactionID" => $transaction->transaction_number,
            "address" => $transaction->to_wallet_address,
            "currency" => 'TRC20',
            "amount" => $transaction->transaction_amount,
            "TxID" => $transaction->txn_hash,
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
        $setting_coin = SettingCoin::find($request->setting_coin_id);
        $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
        $wallet = Wallet::find($request->wallet_id);
        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
        }
        $withdrawal_fee = Setting::where('slug', 'withdrawal-fee')->latest()->first();
        $final_amount = $amount - $withdrawal_fee->value;
        $wallet->balance -= $amount;
        $wallet->save();

        $transaction_id = RunningNumberService::getID('transaction');

        $transaction = Transaction::create([
            'category' => 'wallet',
            'user_id' => $user->id,
            'from_wallet_id' => $request->wallet_id,
            'transaction_number' => $transaction_id,
            'transaction_type' => 'Withdrawal',
            'amount' => $amount,
            'transaction_amount' =>  $final_amount,
            'transaction_charges' => $request->payment_charges,
            'to_wallet_address' => $request->wallet_address,
            'status' => 'Processing',
            'new_wallet_amount' => $wallet->balance,
            'new_coin_amount' => $coin->unit,
        ]);

        $payout = config('payout-setting');
        $hashedToken = md5('metafinx@support.com' . $payout['apiKey']);
        $params = [
            "token" => $hashedToken,
            "transactionID" => $transaction->transaction_number,
            "address" => $transaction->to_wallet_address,
            "currency" => 'TRC20',
            "amount" => $transaction->transaction_amount,
            "payment_charges" => $transaction->transaction_charges,
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

        $transaction = Transaction::query()
            ->where('transaction_number', $result['transactionID'])
            ->first();

        $dataToHash = md5($transaction->transaction_number . $transaction->to_wallet_address);

        if ($result['token'] === $dataToHash) {
            //proceed approval
            $transaction->update([
                'status' => $result['status'],
                'remarks' => $result['remarks']
            ]);
            if ($transaction->status =='Success') {
                if ($transaction->transaction_type == 'Deposit') {
                    $wallet = Wallet::find($transaction->to_wallet_id);

                    $wallet->update([
                        'balance' => $wallet->balance + $transaction->transaction_amount
                    ]);
                }
            } else {
                if ($transaction->transaction_type == 'Withdrawal') {
                    $wallet = Wallet::find($transaction->from_wallet_id);

                    $wallet->update([
                        'balance' => $wallet->balance + $transaction->amount
                    ]);
                }

                PaymentStatus::create([
                    'message' => 'Payment with ID ' . $transaction->id . ', STATUS is ' . $transaction->status
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Deposit Success']);
    }
}
