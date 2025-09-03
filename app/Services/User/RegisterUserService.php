<?php

namespace App\Services\User;

use App\Http\Request\User\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterUserService
{
    public function __invoke(RegisterUserRequest $request): array
    {
        // Les champs sont déjà validés par le DTO
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name', '');
        $answer = $request->input('answer');
        $id = $request->input('id');
        $numberdays = $request->input('days');

        $passwordHashed = Hash::make($password);
        $answerHashed = $answer ? Hash::make($answer) : null;

        $validDate = null;
        if ($numberdays && $numberdays !== 'A vie') {
            $validDate = Carbon::today()
                ->addDays(intval($numberdays))
                ->format('Y-m-d');
        }

        try {
            User::create([
                'NAME' => $name,
                'EMAIL' => $email,
                'PASSWORD' => $passwordHashed,
                'QUEST_ID' => $id,
                'QUEST_ANSWER' => $answerHashed,
                'VALID_DATE' => $validDate
            ]);

            return [
                'success' => true,
                'http_code' => 201, // Création OK
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
