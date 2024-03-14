<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'wallet_id' => ['required'],
            'email' => ['required', 'exists:users,email'],
            'amount' => ['required', 'numeric'],
            // 'terms' => ['accepted'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'wallet_id' => 'Internal Wallet',
            'email' => 'Email',
            'amount' => 'Amount',
            // 'terms' => 'Terms',
        ];
    }
}
