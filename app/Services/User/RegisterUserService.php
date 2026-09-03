<?php

namespace App\Services\User;

use App\Http\Request\User\RegisterUserRequest;
use App\Models\PaymentOrder;
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

        // Inscription avec abonnement payant immédiat (pas l'essai gratuit, ni une
        // inscription gratuite simple) : exige un paiement réellement capturé pour
        // ce palier, jamais une simple confiance du "days" envoyé par le client.
        $order = null;
        if ($numberdays) {
            $order = PaymentOrder::where('email', $email)
                ->where('days', $numberdays)
                ->where('status', 'captured')
                ->latest()
                ->first();

            if (!$order) {
                return [
                    'success' => false,
                    'error' => "Aucun paiement validé trouvé pour cette offre.",
                    'http_code' => 400,
                ];
            }
        }

        $passwordHashed = Hash::make($password);
        $answerHashed = $answer ? Hash::make($answer) : null;

        $validDate = null;
        $lifetimePurchased = false;
        if ($numberdays === 'A vie') {
            $lifetimePurchased = true;
        } elseif ($numberdays) {
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
                'VALID_DATE' => $validDate,
                'LIFETIME_PURCHASED' => $lifetimePurchased,
            ]);

            $order?->update(['status' => 'consumed']);

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
