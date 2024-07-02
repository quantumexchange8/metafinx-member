<?php

namespace App\Http\Controllers;

use App\Notifications\DepositRequestNotification;
use Http;
use App\Models\Coin;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SettingCoin;
use App\Models\Transaction;
use App\Models\Token;
use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\SettingWithdrawalFee;
use App\Http\Requests\DepositRequest;
use App\Services\RunningNumberService;
use App\Http\Requests\WithdrawalRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function deposit(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $wallet = Wallet::find($request->wallet_id);
        $deposit_charge = Setting::where('slug', 'deposit-fee')->latest()->first();
        $amount = $request->amount;
        $transaction_charge = $amount * ($deposit_charge->value / 100);
        
        $transaction_id = RunningNumberService::getID('transaction');
        
        // $transaction = Transaction::create([
        //     'category' => 'wallet',
        //     'user_id' => $user->id,
        //     'to_wallet_id' => $request->wallet_id,
        //     'transaction_number' => $transaction_id,
        //     'txn_hash' => $request->txn_hash,
        //     'transaction_type' => 'Deposit',
        //     'amount' => $amount,
        //     'transaction_charges' => $transaction_charge,
        //     'transaction_amount' => $amount * ((100 - $deposit_charge->value) / 100),
        //     'to_wallet_address' => $request->to_wallet_address,
        //     'status' => 'Processing',
        //     'new_wallet_amount' => $wallet->balance,
        // ]);

        $payoutSetting = config('payment-gateway');
        $domain = $_SERVER['HTTP_HOST'];
        $paymentGateway = config('payment-gateway');
        $intAmount = intval($amount * 1000000);

        if ($domain === 'login.metafinx.com') {
            $selectedPayout = $payoutSetting['live'];
        } else {
            $selectedPayout = $payoutSetting['staging'];
        }

        $vCode = md5($intAmount . $selectedPayout['appId'] . $transaction_id . $selectedPayout['merchantId']);

        $params = [
            'amount' => $intAmount,
            'orderNumber' => $transaction_id,
            'userId' => $user->id,
            'merchantId' => $selectedPayout['merchantId'],
            'vCode' => $vCode,
        ];

        // Send response
        $url = $selectedPayout['paymentUrl'] . '/payment';
        $redirectUrl = $url . "?" . http_build_query($params);

        return Inertia::location($redirectUrl);

        // return redirect()->route('testing_payment.tesing_for_payment_gateway', [
        //     'user' => $user,
        //     'wallet' => $wallet,
        //     'amount' => $amount,
        //     'to_wallet_address' => $request->to_wallet_address,
        //     'token' => $token,
        //     'transaction_number' => $transaction_id,
        // ]);
    }

    public function withdrawal(WithdrawalRequest $request)
    {
        $user = \Auth::user();
        $amount = number_format(floatval($request->amount), 2, '.', '');
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
        ]);

        $payoutSetting = config('payout-setting');
        $domain = $_SERVER['HTTP_HOST'];

        if ($domain === 'login.metafinx.com') {
            $selectedPayout = $payoutSetting['live'];
        } else {
            $selectedPayout = $payoutSetting['staging'];
        }

        $hashedToken = md5($selectedPayout['email'] . $selectedPayout['apiKey']);

        $params = [
            "token" => $hashedToken,
            "transactionID" => $transaction->transaction_number,
            "address" => $transaction->to_wallet_address,
            "currency" => 'TRC20',
            "amount" => $transaction->transaction_amount,
            "payment_charges" => $transaction->transaction_charges,
        ];

        $url = $selectedPayout['base_url'] . '/receiveWithdrawal';

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

        return response()->json(['success' => true, 'message' => trans('public.deposit_success')]);
    }

    public function deposit_approval(Request $request)
    {
        $transaction = Transaction::find($request->id);
        $status = $request->status;
        $transaction->status = $status;
        $transaction->remarks = ($status == 'Success') ? 'System Approve' : 'System Reject';
        $transaction->approval_date = now();
        $transaction->save();

        if ($status == 'Success') {
            $wallet = Wallet::find($transaction->to_wallet_id);
            $wallet->balance += $transaction->transaction_amount;
            $wallet->save();

            $transaction->update([
                'new_wallet_amount' => $wallet->balance
            ]);
        }

        $this->updateTransaction($transaction);

        $title = ($status == 'Success') ? 'Transaction Approved' : 'Transaction Rejected';
        $message = ($status == 'Success') ? 'Successfully APPROVED Transaction Number: ' : 'Successfully REJECTED Transaction Number: ';
        $messageStatus = ($status == 'Success') ? 'success' : 'warning';

        return redirect()->back()->with('title', $title)->with($messageStatus, $message . $transaction->transaction_number);
    }

    private function updateTransaction($rec)
    {
        $hashedToken = md5($rec->transaction_number . $rec->to_wallet_address);

        $domain = $_SERVER['HTTP_HOST'];
        $payout = config('payout-setting');
        $email = ($domain == 'login.metafinx.com') ? $payout['live']['email'] : $payout['staging']['email'];
        $url = ($domain == 'login.metafinx.com') ? 'https://privateadmin.ttpays.io/updateTransaction' : 'https://thundertrade.currenttech.pro/updateTransaction';

        $params = [
            "token" => $hashedToken,
            "transactionID" => $rec->transaction_number,
            "address" => $rec->to_wallet_address,
            "amount" => $rec->amount,
            "status" => ($rec->status == 'Success') ? 2 : 1,
            "remarks" => $rec->remarks,
            "email" => $email,
        ];

        $response = \Illuminate\Support\Facades\Http::post($url, $params);

        \Log::debug($response);
    }
}
