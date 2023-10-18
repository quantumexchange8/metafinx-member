<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepositExport implements FromCollection, WithHeadings
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
        foreach($records as $deposits){
            $result[] = array(
                'asset' => $deposits->wallet->name,
                'transaction_id' => $deposits->transaction_id,
                'date' => Carbon::parse($deposits->created_at)->format('Y-m-d'),
                'amount' =>  number_format((float)$deposits->amount, 2, '.', ''),
                'status' => $deposits->status,
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
            'Amount',
            'Status',
        ];
    }
}
