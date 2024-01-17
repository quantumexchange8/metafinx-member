<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InternalTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_wallet_id' => ['required'],
            'to_wallet_id' => ['required'],
            'amount' => ['required', 'numeric'],
            'terms' => ['accepted'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'from_wallet_id' => 'Transfer Type',
            'to_wallet_id' => 'Transfer Type',
            'amount' => 'Amount',
            'terms' => 'Terms',
        ];
    }
}
