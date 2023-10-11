<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Wallet;
use Illuminate\Console\Command;

class CheckDepositStatusCommand extends Command
{
    protected $signature = 'check:deposit-status';

    protected $description = 'Check deposit statuses and update as needed';

    public function handle(): void
    {
        $pendingPayments = Payment::where('status', 'Pending')
            ->whereBetween('created_at', [now()->subMinutes(30), now()])
            ->get();

        foreach ($pendingPayments as $payment) {
            $response = \Http::get('https://apilist.tronscanapi.com/api/transaction-info', [
                'hash' => $payment->txn_hash
            ]);

            if ($response->successful()) {
                $transactionInfo = $response->json();

                $amount_str = $transactionInfo['trc20TransferInfo'][0]['amount_str'];
                $crypto_amount = $payment->amount * 1000000;
                $range = 5;
                // Update the status of the payment based on the transaction info
                if ($transactionInfo['contractRet'] == 'SUCCESS' && $transactionInfo['confirmed'] && $transactionInfo['trc20TransferInfo'][0]['to_address'] == $payment->to_wallet_address) {
                    if ($amount_str >= $crypto_amount && $amount_str <= $crypto_amount + $range ) {
                        $payment->update([
                            'status' => 'Success'
                        ]);

                        $wallet = Wallet::find($payment->wallet_id);

                        $wallet->update([
                            'balance' => $payment->amount
                        ]);
                    } else {
                        $payment->update([
                            'status' => 'Processing'
                        ]);
                    }
                }
                PaymentStatus::create([
                    'message' => 'Processed payment with ID ' . $payment->id . ', STATUS is ' . $payment->status
                ]);
            }
        }
    }
}
