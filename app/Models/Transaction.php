<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'category',
        'transaction_type',
        'from_wallet_id',
        'to_wallet_id',
        'from_coin_id',
        'to_coin_id',
        'transaction_number',
        'to_wallet_address',
        'txn_hash',
        'amount',
        'transaction_charges',
        'transaction_amount',
        'new_wallet_amount',
        'new_coin_amount',
        'unit',
        'price_per_unit',
        'status',
        'remarks',
        'approval_date',
        'handle_by',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function fromWallet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id', 'id');
    }

    public function toWallet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id', 'id');
    }

    public function fromCoin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Coin::class, 'from_coin_id', 'id');
    }

    public function toCoin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Coin::class, 'to_coin_id', 'id');
    }

    public function coinStacking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CoinStacking::class, 'user_id', 'user_id')->select(['user_id', 'investment_plan_id']);
    }

}
