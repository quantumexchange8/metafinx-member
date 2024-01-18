<?php

namespace App\Exports;

use App\Models\SettingRank;
use App\Models\InvestmentSubscription;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AffiliateTableExport implements FromCollection, WithHeadings
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $records = $this->query->latest()->get();
        $result = array();
        foreach($records as $child){
            $result[] = array(
                'name' => $child->name,
                'email' => $child->email,
                'upline_name' => $child->upline->name ?? null,
                'upline_email' => $child->upline->email ?? null,
                'created_at' => $child->created_at,
                'setting_rank_name' => $this->getSettingRankName($child->setting_rank_id),
                'level' => $child->getLevel() - 1,
                'total_affiliate' => strval(count($child->getChildrenIds())),
                'valid_self_deposit' => strval($this->getSelfDeposit($child)),
                'valid_affiliate_deposit' => strval($this->getValidAffiliateDeposit($child)),
            );
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Referrer Name',
            'Referrer Email',
            'Joining Date',
            'Rank',
            'Generation',
            'Total Affiliate',
            'Valid Self Deposit',
            'Valid Affiliate Deposit',
        ];
    }
    protected function getSelfDeposit($user)
    {
        return InvestmentSubscription::query()
            ->where('user_id', $user->id)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    protected function getValidAffiliateDeposit($user)
    {
        $ids = $user->getChildrenIds();

        return InvestmentSubscription::query()
            ->whereIn('user_id', $ids)
            ->whereDate('expired_date', '>', now())
            ->sum('amount');
    }

    protected function getSettingRankName($settingRankId)
    {
        $settingRank = SettingRank::find($settingRankId);

        return $settingRank ? $settingRank->name : null;
    }

}
