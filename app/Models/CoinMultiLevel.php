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
        'coin_stacking_id',
        'coin_stacking_amount',
    ];

    public function getLastChild($position)
    {
        // Retrieve the direct left child
        $directChild = $this->direct_child($position)->first();

        // Check if direct left child exists
        $child = $directChild->children()->where('position', $position)->latest()->first();

        return CoinMultiLevel::where('hierarchy_list', 'LIKE', '%' . $child->hierarchy_list . '%')
            ->with('user:id,name,email')
            ->whereHas('upline', function ($query) use ($position) {
                $query->where('position', $position);
            })
            ->where('position', $position)
            ->orderBy('id', 'desc') // Assuming 'id' is the primary key
            ->first();
    }

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

    public function direct_child($position): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CoinMultiLevel::class, 'upline_id', 'id')->where('position', $position);
    }
}
