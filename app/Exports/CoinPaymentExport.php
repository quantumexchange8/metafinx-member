<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoinPaymentExport implements FromCollection
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
        foreach($records as $coinpayment){
            $result[] = array(
                'from' => $coinpayment->wallet->name,
                'transaction_id' => $coinpayment->transaction_id,
                'date' => Carbon::parse($coinpayment->created_at)->format('Y-m-d'),
                'amount' =>  number_format((float)$coinpayment->amount, 2, '.', ''),
                'unit' =>  $coinpayment->unit,
                'status' => $coinpayment->status,
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'From',
            'Transaction ID',
            'Date',
            'Paid',
            'Unit',
            'Status',
        ];
    }
}
