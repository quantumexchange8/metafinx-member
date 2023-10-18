<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WithdrawalExport implements FromCollection, WithHeadings
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $records = $this->query->get();
        $result = array();
        foreach($records as $withdrawal){
            $result[] = array(
                'asset' => $withdrawal->wallet->name,
                'transaction_id' => $withdrawal->transaction_id,
                'date' => Carbon::parse($withdrawal->created_at)->format('Y-m-d'),
                'to_wallet_address' =>  $withdrawal->to_wallet_address,
                'amount' =>  number_format((float)$withdrawal->amount, 2, '.', ''),
                'status' => $withdrawal->status,
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Asset',
            'Transaction ID',
            'Date',
            'Wallet Address',
            'Amount',
            'Status',
        ];
    }
}
