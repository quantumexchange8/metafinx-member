<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Earning;
use App\Models\InvestmentSubscription;
use App\Models\Payment;
use App\Models\SettingWithdrawalFee;
use App\Models\Wallet;
use App\Models\SettingWalletAddress;
use App\Services\RunningNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'min:20'],
            'txn_hash' => ['required'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'wallet_id' => 'Wallet',
            'amount' => 'Amount',
            'txn_hash' => 'TXN Hash',
            'terms' => 'Terms and Conditions'
        ]);

        if (!$validator->passes()){
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {
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
            $response = \Http::post($url, $params);
            \Log::debug($response);

            return response()->json([
                'status' => 'success',
                'message' => 'The deposit request has been submitted successfully.',
                'transaction_detail' => $payment
            ]);
        }
    }

    public function withdrawal(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:50'],
            'wallet_id' => ['required'],
            'wallet_address' => ['required'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'amount' => 'Amount',
            'wallet_id' => 'Wallet',
            'wallet_address' => 'Wallet Address',
            'terms' => 'Terms and Conditions'
        ]);

        if (!$validator->passes()){
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = \Auth::user();
            $amount = floatval($request->amount);
            $wallet = Wallet::find($request->wallet_id);
            if ($wallet->balance < $amount) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Insufficient Balance'
                ]);
            }
            $wallet->balance -= $amount;
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

            return response()->json([
                'status' => 'success',
                'message' => 'The withdrawal request has been submitted successfully.',
                'transaction_detail' => $payment
            ]);
        }
    }

    public function transaction_history()
    {
        $user = \Auth::user();

        $transactions = Payment::with('wallet:id,user_id,name,balance')->where('user_id', $user->id)->get();
        $earnings = Earning::where('upline_id', $user->id)->select('id', 'upline_id', 'after_amount', 'type', 'created_at')->get();
        $investments = InvestmentSubscription::query()
            ->with('investment_plan:id,name,roi_per_annum,investment_period')
            ->where('user_id', $user->id)
            ->get();

        $locale = app()->getLocale(); // Get the current locale

        $investmentSubscriptions = $investments->map(function ($investmentSubscription) use ($locale) {
            return [
                'id' => $investmentSubscription->id,
                'plan_name' => [
                    'name' => $investmentSubscription->investment_plan->getTranslation('name', 'en'),
                ],
                'subscription_id' => $investmentSubscription->subscription_id,
                'amount' => $investmentSubscription->amount,
                'created_at' => $investmentSubscription->created_at,
            ];
        });

        return response()->json([
            'transactions' => $transactions,
            'earnings' => $earnings,
            'subscriptions' => $investmentSubscriptions,
        ]);
    }

    public function setting_wallet_address()
    {
        $wallet_address = SettingWalletAddress::all();

        return response()->json([
            'wallet_address' => $wallet_address,
            'withdrawalFee' => SettingWithdrawalFee::latest()->first(),
        ]);
    }

    public function notifications()
    {
        $user = \Auth::user();
        $notifications = $user->notifications;

        $userNotifications = [];
        foreach ($notifications as $notification) {
            $data = $notification->data;
            $read = $notification->read_at;
            $mergedData = [
                "id" => $notification->id,
                "title" => $data['title'],
                "content" => $data['content'],
                "created_at" => Carbon::parse($data['post_date'])->format('Y-m-d h:m:s'),
                "image_address" => $data['image'] ?? '',
                "read" => is_null($read) ? null : Carbon::parse($read)->format('Y-m-d h:m:s'),
            ];

            $userNotifications[] = $mergedData;
        }

        return response()->json([
            'notifications' => $userNotifications,
        ]);
    }

    public function read_notification(Request $request)
    {
        $user = \Auth::user();
        $notifications = $user->unreadNotifications;

        $title = 'Notification unavailable';
        $read = null;

        foreach ($notifications as $notification) {
            if ($notification->id == $request->id && $notification->read_at == null) {
                $notification->markAsRead();
                $title = $notification->data['title'];
                $read = Carbon::parse($notification->read_at)->format('Y-m-d h:m:s');
            }
        }

        return response()->json([
            'title' => $title,
            'read_at' => $read
        ]);
    }
}
