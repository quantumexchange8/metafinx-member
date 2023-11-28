<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Earning;
use App\Models\InvestmentSubscription;
use App\Models\Payment;
use App\Models\Wallet;
use App\Services\RunningNumberService;
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

            $hashedToken = md5('MetaFinXmetafinx@support.com');
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
            'amount' => ['required', 'numeric', 'min:20'],
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
                throw ValidationException::withMessages(['amount' => trans('Insufficient balance')]);
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
                'to_wallet_address' => $request->wallet_address,
                'status' => 'Processing'
            ]);

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

        $transactions = Payment::where('user_id', $user->id)->get();
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
}
