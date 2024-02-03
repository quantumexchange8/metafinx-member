<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Coin;
use App\Models\Wallet;
use App\Models\Earning;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\CoinPrice;
use App\Models\CoinPayment;
use App\Models\SettingCoin;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CoinMarketTime;
use App\Models\ConversionRate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SettingWalletAddress;
use App\Models\SettingWithdrawalFee;
use App\Services\RunningNumberService;
use App\Http\Requests\InternalTransferRequest;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'to_wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'min:20'],
            'txn_hash' => ['required'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'to_wallet_id' => 'Wallet',
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
            $wallet = Wallet::find($request->wallet_id);
            $transaction_id = RunningNumberService::getID('transaction');

            $transaction = Transaction::create([
                'category' => 'wallet',
                'user_id' => $user->id,
                'to_wallet_id' => $request->to_wallet_id,
                'transaction_number' => $transaction_id,
                'txn_hash' => $request->txn_hash,
                'transaction_type' => 'Deposit',
                'amount' => $request->amount,
                'transaction_charges' => 0,
                'transaction_amount' => $request->amount,
                'to_wallet_address' => $request->to_wallet_address,
                'status' => 'Pending',
                'new_wallet_amount' => $wallet->balance,
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
            $response = \Http::post($url, $params);
            \Log::debug($response);

            return response()->json([
                'status' => 'success',
                'message' => 'The deposit request has been submitted successfully.',
                'transaction_detail' => $transaction
            ]);
        }
    }

    public function withdrawal(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:50'],
            'from_wallet_id' => ['required'],
            'wallet_address' => ['required'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'amount' => 'Amount',
            'from_wallet_id' => 'Wallet',
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
            $wallet = Wallet::find($request->from_wallet_id);
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
                'from_wallet_id' => $request->from_wallet_id,
                'transaction_number' => $transaction_id,
                'transaction_type' => 'Withdrawal',
                'amount' => $amount,
                'transaction_amount' =>  $final_amount,
                'transaction_charges' => $request->transaction_charges,
                'to_wallet_address' => $request->wallet_address,
                'status' => 'Processing',
                'new_wallet_amount' => $wallet->balance,
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

            // $url = $payout['base_url'] . '/receiveWithdrawal';
            // $response = \Http::post($url, $params);

            return response()->json([
                'status' => 'success',
                'message' => 'The withdrawal request has been submitted successfully.',
                'transaction_detail' => $transaction
            ]);
        }
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

    public function setting_coin()
    {
        $setting_coins = SettingCoin::select('id', 'name', 'symbol')->get();

        return response()->json([
            'setting_coin' => $setting_coins,
        ]);
    }

    public function user_coins()
    {
        $user = \Auth::user();

        $coins = Coin::where('user_id', $user->id)->select('id', 'address', 'unit', 'price', 'amount')->get();
        $coin_price = CoinPrice::whereDate('price_date', today())->first() ?? CoinPrice::latest('price_date')->first();
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first();

        $coins = $coins->map(function ($coin) use ($coin_price, $coin_price_yesterday) {
            $priceDiffPercentage = 0;
            if ($coin_price_yesterday && $coin_price_yesterday->price != 0) {
                $priceDiffPercentage = number_format($coin_price->price / $coin_price_yesterday->price, 2);
            }

            $amountPrefix = '';
            if ($coin_price_yesterday && $coin_price_yesterday->price > $coin_price->price) {
                $amountPrefix = '-';
            } else {
                $amountPrefix = '';
            }

            $coin->price_diff_percentage = $amountPrefix . $priceDiffPercentage;

            return $coin;
        });

        return response()->json([
            'coin' => $coins,
        ]);
    }

    public function coinMarket()
    {
        $coin_prices = CoinPrice::whereDate('price_date', '<=', now()->endOfDay())->select('id', 'setting_coin_id', 'updated_by', 'price', 'price_date')->get();
        $conversion_rate = ConversionRate::latest()->first();
        $coinMarketTime = CoinMarketTime::latest()->select('id', 'setting_coin_id', 'open_time', 'close_time', 'frequency_type')->get();

        $coinMarketData = [
            'coin_prices' => $coin_prices,
            'conversion_rate' => $conversion_rate,
            'coin_market_time' => $coinMarketTime,
        ];

        return response()->json([
            'coin_market' => $coinMarketData,
        ]);

    }

    public function buy_coin(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'unit' => ['required', 'numeric'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'amount' => 'Amount',
            'unit' => 'Unit',
            'terms' => 'Terms and Conditions'
        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = \Auth::user();

            $transaction_id = RunningNumberService::getID('transaction');
            $setting_coin = SettingCoin::find($request->setting_coin_id);
            $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
            $total_unit = $coin->unit + $request->unit;

            $wallet = Wallet::find($request->wallet_id);

            if ($wallet->balance < $request->transaction_amount) {
                throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance') . ', PAYABLE AMOUNT: $' . $request->transaction_amount]);
            }

            $wallet->decrement('balance', $request->transaction_amount);

            $transaction = Transaction::create([
                'category' => 'asset',
                'user_id' => $user->id,
                'transaction_type' => 'BuyCoin',
                'from_wallet_id' => $request->wallet_id,
                'to_coin_id' => $request->coin_id,
                'transaction_number' => $transaction_id,
                'unit' => $request->unit,
                'price_per_unit' => $request->price,
                'amount' => $request->amount,
                'transaction_charges' => $request->gas_fee,
                'transaction_amount' => $request->transaction_amount,
                'status' => 'Success',
                'new_wallet_amount' => $wallet->balance,
                'new_coin_amount' => $coin->unit,
            ]);

            $coin->update([
                'unit' => $total_unit,
                'price' => $transaction->price_per_unit,
            ]);

            $accumulate_supply = $setting_coin->accumulate_supply + $transaction->unit;
            $accumulate_capped = $setting_coin->accumulate_capped + ($accumulate_supply * 2);

            $setting_coin->update([
                'accumulate_supply' => $accumulate_supply,
                'accumulate_capped' => $accumulate_capped,
            ]);

            $total_amount = $coin->unit / $coin->price;

            $coin->update([
                'amount' => $total_amount,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'The coin has been purchased successfully.',
                'transaction_detail' => $transaction,
                'coin' => $coin,
            ]);
        }
    }

    public function wallet_history()
    {
        $user = \Auth::user();

        $wallets = Transaction::with('fromWallet:id,user_id,name,balance', 'toWallet:id,user_id,name,balance')
            ->where('user_id', $user->id)->where('category', 'wallet')
            ->get();

        return response()->json(['wallets' => $wallets]);
    }

    public function asset_history()
    {
        $user = \Auth::user();

        $assets = Transaction::with('fromWallet:id,user_id,name,balance', 'toWallet:id,user_id,name,balance')
            ->where('user_id', $user->id)->where('category', 'asset')
            ->get();

        return response()->json(['assets' => $assets]);
    }

    public function internal_transfer(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'amount' => 'Amount',
            'terms' => 'Terms and Conditions'
        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {

            $user = \Auth::user();
            $from_wallet = Wallet::find($request->from_wallet_id);
            $to_wallet = Wallet::find($request->to_wallet_id);

            if ($from_wallet->id == $to_wallet->id) {
                throw ValidationException::withMessages(['from_wallet_id' => 'Wallet cannot be the same']);
            }

            if ($from_wallet->balance < $request->amount) {
                throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
            }

            $transaction_number = RunningNumberService::getID('transaction');

            $transaction = Transaction::create([
                'category' => 'wallet',
                'user_id' => $user->id,
                'transaction_type' => 'InternalTransfer',
                'from_wallet_id' => $from_wallet->id,
                'to_wallet_id' => $to_wallet->id,
                'transaction_number' => $transaction_number,
                'amount' => $request->amount,
                'transaction_charges' => 0,
                'transaction_amount' => $request->amount,
                'status' => 'Success',
                'remarks' => $request->remarks,
                'new_wallet_amount' => $to_wallet->balance,
            ]);

            // Update the wallet balance
            $from_wallet->update([
                'balance' => $from_wallet->balance - $transaction->transaction_amount,
            ]);

            $to_wallet->update([
                'balance' => $to_wallet->balance + $transaction->transaction_amount,
            ]);

            $transaction->update([
                'new_wallet_amount' => $to_wallet->balance,
            ]);    

            return response()->json([
                'status' => 'success',
                'message' => 'The internal transfer has been successfully performed.',
                'transaction_detail' => $transaction,
            ]);
        }
    }

    public function getCoinChart(Request $request)
    {
        $coinPrices = CoinPrice::query()
        ->when($request->filled('month'), function ($query) use ($request) {
            $monthsAgo = now()->subMonths($request->input('month'));
            $query->where('price_date', '>=', $monthsAgo);
        })
        ->whereDate('price_date', '<=', now())
        ->select(
            DB::raw('DATE(price_date) as date'),
            DB::raw('SUM(price) as price')
        )
        ->groupBy('date')
        ->get();
            
        $labels = [];
        $data = [];
    
        foreach ($coinPrices as $priceData) {
            // Format date as 'j M'
            $formattedDate = Carbon::parse($priceData->date)->format('j M');
    
            $labels[] = $formattedDate;
            $data[] = $priceData->price;
        }
        $coin_price = CoinPrice::whereDate('price_date', today())->first()->price ?? CoinPrice::latest('price_date')->first()->price;
        $coin_price_yesterday = CoinPrice::whereDate('price_date', '<', today())->latest()->first()->price;

        $borderColor = '#12b76a66';
        if ($coin_price > $coin_price_yesterday) {
            $borderColor = '#12B76A';
        } elseif ($coin_price < $coin_price_yesterday) {
            $borderColor = '#F04438';
        } else {
            $borderColor = '#9DA4AE';
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'pointStyle' => false,
                    'fill' => true,
                ],
            ],
        ];
    
        return response()->json($chartData);
    }
    
    public function swap_coin(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'unit' => ['required', 'numeric'],
            'terms' => ['accepted']
        ])->setAttributeNames([
            'unit' => 'Unit',
            'terms' => 'Terms and Conditions'
        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status' => 'fail',
                'error' => $validator->errors()->toArray()
            ]);
        } else {

            $user = \Auth::user();
            $transaction_id = RunningNumberService::getID('transaction');
            $setting_coin = SettingCoin::find($request->setting_coin_id);
            $coin = Coin::where('user_id', $user->id)->where('setting_coin_id', $setting_coin->id)->first();
            $total_unit = $coin->unit - $request->unit;

            $wallet = Wallet::find($request->wallet_id);

            if ($coin->unit < $request->unit) {
                throw ValidationException::withMessages(['unit' => trans('public.insufficient_unit') . ', AVAILABLE UNIT: ' . $coin->unit . ' ' . $setting_coin->name ]);

            }

            $wallet->increment('balance', $request->transaction_amount);

            $transaction = Transaction::create([
                'category' => 'asset',
                'user_id' => $user->id,
                'transaction_type' => 'SwapCoin',
                'to_wallet_id' => $request->wallet_id,
                'from_coin_id' => $request->coin_id,
                'transaction_number' => $transaction_id,
                'unit' => $request->unit,
                'price_per_unit' => $request->price,
                'amount' => $request->amount,
                'transaction_charges' => $request->gas_fee,
                'transaction_amount' => $request->transaction_amount,
                'status' => 'Success',
                'new_wallet_amount' => $wallet->balance,
                'new_coin_amount' => $total_unit,
            ]);

            $coin->update([
                'unit' => $total_unit,
                'price' => $transaction->price_per_unit,
            ]);

            $total_amount = $coin->unit / $coin->price;

            $coin->update([
                'amount' => $total_amount,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'The Swap Coin has been successfully performed.',
                'transaction_detail' => $transaction,
            ]);
        }
    }
}
