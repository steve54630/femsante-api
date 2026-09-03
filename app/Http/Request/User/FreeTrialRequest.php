<?php

namespace App\Http\Request\User;

use Illuminate\Foundation\Http\FormRequest;

class FreeTrialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Veuillez fournir un email',
            'password.required' => 'Veuillez fournir un mot de passe',
        ];
    }
}
