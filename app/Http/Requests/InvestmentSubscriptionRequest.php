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
            'wallet_id' => trans('public.wallet.wallet'),
            'amount' => trans('public.wallet.amount'),
            'unit_number' => trans('public.earn.unit_number'),
            'housing_price' => trans('public.earn.housing_price'),
            'terms' => trans('public.earn.t&c'),
        ];
    }
}
