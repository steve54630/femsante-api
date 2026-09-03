<?php

namespace App\Services\User;

use App\Http\Request\User\ConnectUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ConnectUserService
{
    public function __invoke(ConnectUserRequest $request): array
    {
        // Récupération directe avec valeur par défaut null
        $email = $request->input('email');
        $password = $request->input('password');

        // Récupération de l'utilisateur par email
        $user = User::where('EMAIL', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'error' => 'Utilisateur incorrect',
                'http_code' => 400,
            ];
        }

        if (!Hash::check($password, $user->PASSWORD)) {
            return [
                'success' => false,
                'error' => 'Mot de passe incorrect',
                'http_code' => 400,
            ];
        }

        // Modèle freemium : un abonnement expiré (ou jamais souscrit) n'empêche plus la
        // connexion — elle navigue en gratuit, 'acces' indique simplement l'absence d'accès
        // premium.
        return [
            'success' => true,
            'user' => $user,
            'acces' => $user->hasAccess(),
            'A vie' => $user->LIFETIME_PURCHASED,
            'http_code' => 200,
        ];
    }
}
