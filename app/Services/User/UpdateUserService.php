<?php

namespace App\Services\User;

use App\Http\Request\User\UpdateUserRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UpdateUserService
{
    public function __invoke(UpdateUserRequest $request): array
    {
        $email = $request->email();
        $password = $request->password();
        $numberdays = $request->days();
        $update = $request->update();

        $user = User::where('EMAIL', $email)->first();

        if (!$user || !Hash::check($password, $user->PASSWORD)) {
            return [
                'success' => false,
                'error' => 'Utilisateur ou mot de passe incorrect',
                'http_code' => 400,
            ];
        }

        try {
            if ($numberdays === 'A vie') {
                $user->VALID_DATE = null;
                $user->LIFETIME_PURCHASED = true;
            } else {
                $baseDate = $update && $user->VALID_DATE
                    ? Carbon::parse($user->VALID_DATE)
                    : Carbon::today();

                $user->VALID_DATE = $baseDate->addDays(intval($numberdays))->format('Y-m-d');
            }

            $user->save();

            return [
                'success' => true,
                'acces' => $user->hasAccess(),
                'http_code' => 200,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur système : Veuillez contacter le développeur',
                'http_code' => 500,
            ];
        }
    }
}
