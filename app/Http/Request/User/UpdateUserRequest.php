<?php

namespace App\Http\Request\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ou ajouter une vérification d'auth si nécessaire
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'days' => ['required', 'string'], // "A vie" ou nombre de jours
            'update' => ['sometimes', 'boolean'], // facultatif
        ];
    }

    public function email(): string
    {
        return $this->input('email');
    }

    public function password(): string
    {
        return $this->input('password');
    }

    public function days(): string
    {
        return $this->input('days');
    }

    public function update(): bool
    {
        return $this->boolean('update', false);
    }
}
