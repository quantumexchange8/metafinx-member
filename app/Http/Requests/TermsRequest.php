<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'contents' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'contents' => 'Contents',
        ];
    }
}
