<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturnReportExport implements FromCollection, WithHeadings
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
                'plan' => $record->subscriptionPlan->investment_plan->name . ' - ' . $record->subscriptionPlan->subscription_id,
                'category' => $record->type,
                'amount' =>  number_format((float)$record->after_amount, 2, '.', ''),
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Plan',
            'Category',
            'Amount',
        ];
    }
}
