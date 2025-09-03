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
            'clientId' => 'required',
            'price' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'clientId.required' => 'Le champ "clientId" est obligatoire',
            'price.required' => 'Le champ "price" est obligatoire',
        ];
    }

}