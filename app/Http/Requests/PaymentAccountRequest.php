<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentAccountRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'payment_account_name' => ['required'],
            'payment_platform_name' => ['required'],
            'account_no' => ['required'],
        ];

        if ($this->payment_method == 'Bank') {
            $rules['bank_swift_code'] = ['required'];
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
            'payment_account_name' => trans('public.profile.crypto_wallet_name'),
            'payment_platform_name' => trans('public.profile.tether'),
            'account_no' => trans('public.profile.wallet_address'),
        ];
    }
}
