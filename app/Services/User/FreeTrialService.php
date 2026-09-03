<?php

namespace App\Services\User;

use App\Http\Request\User\FreeTrialRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Active l'essai gratuit de 7 jours (accès premium temporaire), déclenché explicitement par
 * l'utilisatrice — distinct de l'abonnement de 7 jours utilisé en interne pour les tests, qui
 * passe par UpdateUserService avec un "days" générique.
 *
 * Ne peut être activé qu'une seule fois par compte (FREE_TRIAL_USED_AT posé définitivement),
 * et uniquement si le compte n'a pas déjà un accès actif (pour ne pas raccourcir un abonnement
 * payant en cours).
 */
class FreeTrialService
{
    private const TRIAL_DAYS = 7;

    public function __invoke(FreeTrialRequest $request): array
    {
        $email = $request->input('email');
        $password = $request->input('password');

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

        if ($user->FREE_TRIAL_USED_AT !== null) {
            return [
                'success' => false,
                'error' => 'L\'essai gratuit a déjà été utilisé sur ce compte',
                'http_code' => 400,
            ];
        }

        if ($user->hasAccess()) {
            return [
                'success' => false,
                'error' => 'Vous avez déjà un accès actif',
                'http_code' => 400,
            ];
        }

        $user->VALID_DATE = Carbon::today()->addDays(self::TRIAL_DAYS)->format('Y-m-d');
        $user->FREE_TRIAL_USED_AT = Carbon::now();
        $user->save();

        return [
            'success' => true,
            'acces' => $user->hasAccess(),
            'http_code' => 200,
        ];
    }
}
