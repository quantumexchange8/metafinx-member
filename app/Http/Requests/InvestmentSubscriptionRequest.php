<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestmentSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'wallet_id' => ['sometimes', 'required'],
            'amount' => ['required', 'numeric'],
            'unit_number' => ['sometimes', 'required'],
            'housing_price' => ['sometimes', 'required', 'numeric', 'integer'],
            'terms' => ['accepted'],
        ];

        // Validate 'unit' only if the plan type is not 'standard'
        if ($this->investment_plan_type == 'staking') {
            $rules['unit'] = ['required', 'numeric'];
        }

        return $rules;
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
            'unit' => trans('public.wallet.unit'),
            'unit_number' => trans('public.earn.unit_number'),
            'housing_price' => trans('public.earn.housing_price'),
            'terms' => trans('public.earn.t&c'),
        ];
    }
}
