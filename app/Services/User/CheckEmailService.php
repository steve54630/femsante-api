<?php

namespace App\Services\User;

use App\Models\User;

class CheckEmailService
{
    public function __invoke(string $email): array
    {
        if (empty($email)) {
            return [
                'success' => false,
                'error' => 'Erreur système : Veuillez contacter le développeur',
                'http_code' => 400,
            ];
        }

        $exists = User::where('EMAIL', $email)->exists();

        if ($exists) {
            return [
                'success' => false,
                'error' => 'Cet e-mail est déjà inscrit à l\'application.',
                'http_code' => 409,
            ];
        }

        return [
            'success' => true,
            'http_code' => 200,
        ];
    }
}
