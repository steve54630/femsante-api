<?php

namespace App\Http\Request\PayPal;

use Illuminate\Foundation\Http\FormRequest;

class ReductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ici tu peux mettre une logique d'autorisation si nécessaire
        return true;
    }

    public function rules(): array
    {
        return [
            'reductionCode' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'reductionCode.required' => 'Veuillez saisir un code de réduction',
            'reductionCode.string' => 'Le code de réduction doit être une chaîne',
            'reductionCode.max' => 'Le code de réduction est trop long',
        ];
    }
}
