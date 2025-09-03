<?php

namespace App\Services\User;

use App\Http\Request\User\ForgottenPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgottenPasswordService
{
    public function __invoke(ForgottenPasswordRequest $request): array
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $answer = $request->input('answer');
        $id = $request->input('id');

        $user = User::where('EMAIL', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'error' => "Cet email n'existe pas",
                'http_code' => 400,
            ];
        }

        if ($user->QUEST_ID != $id || !Hash::check($answer, $user->QUEST_ANSWER)) {
            return [
                'success' => false,
                'error' => 'Question ou réponse incorrecte',
                'http_code' => 400,
            ];
        }

        if (Hash::check($password, $user->PASSWORD)) {
            return [
                'success' => false,
                'error' => 'Mot de passe trop proche de l\'ancien',
                'http_code' => 400,
            ];
        }

        try {
            $user->PASSWORD = Hash::make($password);
            $user->save();

            return [
                'success' => true,
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
