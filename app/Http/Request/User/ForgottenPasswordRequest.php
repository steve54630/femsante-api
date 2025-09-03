<?php

namespace App\Http\Request\User;

use Illuminate\Foundation\Http\FormRequest;

class ForgottenPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'answer' => 'required|string|max:255',
            'id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Veuillez fournir un email',
            'password.required' => 'Veuillez fournir un mot de passe',
            'answer.required' => 'Veuillez fournir la réponse à la question',
            'id.required' => 'Identifiant manquant',
        ];
    }
}
