<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_account_name',
        'payment_platform',
        'payment_platform_name',
        'account_no',
        'bank_branch_address',
        'bank_swift_code',
        'bank_code',
        'bank_code_type',
        'country',
        'currency',
        'status',
    ];
}
