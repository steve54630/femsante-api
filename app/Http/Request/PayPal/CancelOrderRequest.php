<?php

namespace App\Http\Request\PayPal;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orderId' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'orderId.required' => 'Le champ "orderId" est obligatoire',
            'orderId.string' => 'Le champ "orderId" doit contenir une chaîne de caractères',
            'orderId.max' => 'Le champ "orderId" doit contenir au maximum 255 caractères',
        ];
    }
}
