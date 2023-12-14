<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestmentSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'integer'],
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
            'wallet_id' => 'Wallet',
            'amount' => 'Amount',
            'terms' => 'Terms & Conditions',
        ];
    }
}
