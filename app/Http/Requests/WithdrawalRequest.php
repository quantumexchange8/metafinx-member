<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:20'],
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
            'amount' => 'Amount',
            'wallet_id' => 'Wallet',
            'wallet_address' => 'Wallet Address',
            'terms' => 'Terms and Conditions'
        ];
    }
}
