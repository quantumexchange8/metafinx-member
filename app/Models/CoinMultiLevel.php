<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
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

    public function getLastChild($directChild, $position)
    {
        // If direct child is the current user, return it directly
        if ($directChild->user_id === Auth::id()) {
            return $directChild;
        }

        // Find the deepest child (either left or right)
        $deepestChild = null;
        $currentChild = $directChild;

        while ($currentChild) {
            $leftChild = $currentChild->children()->where('position', $position)->where('position', 'left')->latest()->first();
            $rightChild = $currentChild->children()->where('position', $position)->where('position', 'right')->latest()->first();

            if ($leftChild && $rightChild) {
                // Compare hierarchy lists to determine the deeper child
                $deepestChild = $leftChild->hierarchy_list > $rightChild->hierarchy_list ? $leftChild : $rightChild;
            } elseif ($leftChild) {
                $deepestChild = $leftChild;
            } elseif ($rightChild) {
                $deepestChild = $rightChild;
            } else {
                // If no child exists, break the loop
                break;
            }

            // Move to the next child
            $currentChild = $deepestChild;
        }

        // Return the deepest child found, or the parent if no child exists
        return $deepestChild ?? $directChild;
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
