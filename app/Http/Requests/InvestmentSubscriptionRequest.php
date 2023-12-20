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
            'unit_number' => ['sometimes', 'required'],
            'housing_price' => ['sometimes', 'required', 'numeric', 'integer'],
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
            'unit_number' => 'Unit Number',
            'housing_price' => 'Housing Price',
            'terms' => 'Terms & Conditions',
        ];
    }
}
