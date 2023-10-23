<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class InvestmentPlan extends Model
{
    use SoftDeletes, HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'investment_min_amount',
        'investment_period',
        'roi_per_annum',
        'status',
    ];

    public function descriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvestmentPlanDescription::class, 'investment_plan_id', 'id');
    }
}
