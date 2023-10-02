<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:20'],
            'txn_hash' => ['required'],
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
            'txn_hash' => 'TXN Hash',
            'terms' => 'Terms and Conditions'
        ];
    }
}
