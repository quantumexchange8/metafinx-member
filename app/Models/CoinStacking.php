<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinStacking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'coin_id',
        'transaction_id',
        'investment_plan_id',
        'subscription_number',
        'type',
        'stacking_unit',
        'stacking_price',
        'stacking_fee',
        'total_earning',
        'status',
        'remarks',
        'next_roi_date',
        'expired_date',
        'terminated_date',
        'max_capped_price',
        'auto_assign_at',
    ];

    protected $casts = [
        'next_roi_date' => 'date',
        'expired_date' => 'date',
        'terminated_date' => 'date',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function investment_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id', 'id');
    }

    public function binaryTree(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CoinMultiLevel::class, 'coin_stacking_id', 'id');
    }
}
