<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'referral_code' => ['required', 'exists:users,referral_code'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'referral_code' => trans('public.register.referral_code'),
            'email' => trans('public.login.email'),
            'password' => trans('public.login.password'),
            'name'=> trans('public.profile.name'),
        ];
    }
}
