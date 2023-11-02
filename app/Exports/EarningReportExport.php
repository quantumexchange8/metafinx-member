<?php

namespace App\Exports;

use App\Models\Earning;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EarningReportExport implements FromCollection, WithHeadings
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
                'downline_name' => $record->downline->name,
                'downline_email' => $record->downline->email,
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
            'Referral Name',
            'Referral Email',
            'Category',
            'Amount',
        ];
    }
}
