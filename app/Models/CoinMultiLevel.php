<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoinMultiLevel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'upline_id',
        'hierarchy_list',
        'position',
        'coin_stacking_amount',
    ];

    public function getChildrenIds(): array
    {
        return CoinMultiLevel::query()
            ->where('hierarchy_list', 'like', '%-' . $this->id . '-%')
            ->pluck('id')
            ->toArray();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sponsor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CoinMultiLevel::class, 'sponsor_id', 'id');
    }

    public function upline(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CoinMultiLevel::class, 'upline_id', 'id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CoinMultiLevel::class, 'upline_id', 'id');
    }
}
