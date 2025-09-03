<?php

namespace App\Http\Request\Video;

use Illuminate\Foundation\Http\FormRequest;

class ServeVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ou gérer l'auth si besoin
    }

    public function rules(): array
    {
        return [
            'video' => ['required', 'string'],
            'titre' => ['required', 'string'],
            'type'  => ['required', 'string', 'in:master,480p,720p'], // adapter selon tes qualités
        ];
    }

    public function video(): string
    {
        return $this->query('video');
    }

    public function titre(): string
    {
        return $this->query('titre');
    }

    public function type(): string
    {
        return $this->query('type');
    }
}
