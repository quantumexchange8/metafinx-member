<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentPlanDescription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'investment_plan_id',
        'description',
    ];

    public function investment_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id', 'id');
    }
}
