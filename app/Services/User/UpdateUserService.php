<?php

namespace App\Services\User;

use App\Http\Request\User\UpdateUserRequest;
use App\Models\PaymentOrder;
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

        // L'accès n'est accordé que s'il existe un paiement réellement capturé et
        // encore non consommé, pour ce compte et ce palier exact — jamais sur la
        // simple confiance du "days" envoyé par le client.
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
            $order->update(['status' => 'consumed']);

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
