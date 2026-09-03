<?php

namespace App\Http\Request\PayPal;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'days' => ['required', 'string'], // "30" / "90" / "365" / "A vie"
            'reductionCode' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Le champ "email" est obligatoire',
            'days.required' => 'Le champ "days" est obligatoire',
        ];
    }
}
