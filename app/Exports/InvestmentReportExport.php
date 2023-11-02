<?php

namespace App\Exports;

use App\Models\InvestmentSubscription;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvestmentReportExport implements FromCollection, WithHeadings
{
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $records = $this->query->get();
        $result = array();
        foreach($records as $record){
            $result[] = array(
                'date' => Carbon::parse($record->created_at)->format('Y-m-d'),
                'expired_date' => $record->expired_date,
                'subscription_id' => $record->subscription_id,
                'plan' => $record->investment_plan->name,
                'amount' =>  number_format((float)$record->amount, 2, '.', ''),
                'status' => $record->status,
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Valid Thru',
            'ID Number',
            'Plan',
            'Amount',
            'Status',
        ];
    }
}
