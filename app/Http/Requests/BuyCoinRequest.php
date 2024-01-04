<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyCoinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric'],
            'unit' => ['required',  'numeric',],
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
            'amount' => trans('public.buy_coin.amount'),
            'unit' => trans('public.buy_coin.unit'),
            'terms' => trans('public.earn.t&c')
        ];
    }
}
