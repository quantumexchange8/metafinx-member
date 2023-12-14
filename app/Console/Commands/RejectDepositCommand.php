<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\PaymentStatus;
use Illuminate\Console\Command;

class RejectDepositCommand extends Command
{
    protected $signature = 'reject:deposit';

    protected $description = 'Reject Deposit that exceed 30 minutes';

    public function handle(): void
    {
        $pendingPayments = Payment::where('status', 'Pending')
            ->whereNotBetween('created_at', [now()->subMinutes(30), now()])
            ->get();

        foreach ($pendingPayments as $payment) {
            $payment->update([
                'status' => 'Rejected',
                'remarks' => 'Rejected by system because it exceed 30 minutes'
            ]);

            PaymentStatus::create([
                'message' => 'Processed payment with ID ' . $payment->id . ', STATUS is ' . $payment->status
            ]);
        }
    }
}
