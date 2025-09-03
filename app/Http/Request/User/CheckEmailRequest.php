<?php

namespace App\Http\Request\User;

use Illuminate\Foundation\Http\FormRequest;

class CheckEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // tu peux gérer l'auth si nécessaire
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    public function email(): string
    {
        return $this->input('email');
    }
}
