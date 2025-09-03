<?php

namespace App\Services\User;

use App\Http\Request\User\ConnectUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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

        // Vérifie si l'abonnement est expiré
        if ($user->VALID_DATE && Carbon::parse($user->VALID_DATE)->lt(Carbon::today())) {
            return [
                'success' => false,
                'error' => 'Veuillez renouveler votre abonnement',
                'repay' => true,
                'http_code' => 400,
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'A vie' => is_null($user->VALID_DATE),
            'http_code' => 200,
        ];
    }
}
