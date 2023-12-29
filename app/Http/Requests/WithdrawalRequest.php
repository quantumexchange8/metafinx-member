<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50'],
            'wallet_id' => ['required'],
            'wallet_address' => ['required'],
            'terms' => ['accepted']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'amount' => trans('public.wallet.amount'),
            'wallet_id' => trans('public.wallet.wallet'),
            'wallet_address' => trans('public.wallet.wallet_address'),
            'terms' => trans('public.earn.t&c')
        ];
    }
}
