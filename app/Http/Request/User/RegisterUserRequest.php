<?php

namespace App\Http\Request\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorise toutes les requêtes, tu peux mettre une logique si besoin
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:USERS,EMAIL'],
            'password' => ['required', 'string', 'min:6'],
            'name' => ['nullable', 'string', 'max:255'],
            'answer' => ['nullable', 'string', 'max:255'],
            'id' => ['required', 'integer'],
            'days' => ['nullable', 'string'], // peut être "A vie" ou nombre de jours
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L’email est obligatoire',
            'email.email' => 'L’email doit être valide',
            'email.unique' => 'Cet email existe déjà',
            'password.required' => 'Le mot de passe est obligatoire',
            'id.required' => 'L’ID de la question est obligatoire',
        ];
    }
}
