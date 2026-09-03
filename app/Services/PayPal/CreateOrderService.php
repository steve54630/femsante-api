<?php

namespace App\Services\Paypal;

use App\Http\Request\PayPal\CreateOrderRequest;
use App\Models\PaymentOrder;
use App\Models\Reduction;
use App\Support\SubscriptionOffers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateOrderService
{
    public function __invoke(CreateOrderRequest $request): array
    {
        $email = $request->input('email');
        $days = $request->input('days');
        $reductionCode = $request->input('reductionCode');

        if (!SubscriptionOffers::isValidDays($days)) {
            return [
                'success' => false,
                'error' => 'Offre inconnue',
                'http_code' => 400,
            ];
        }

        $price = SubscriptionOffers::basePrice($days);

        // Le code promo est revalidé ici, côté serveur — jamais fait confiance à un
        // pourcentage envoyé par le client. Ignoré silencieusement s'il ne s'applique
        // pas à ce palier (règle métier : uniquement 12 mois / à vie), même comportement
        // que la prévisualisation côté app.
        if ($reductionCode && SubscriptionOffers::isReductionEligible($days)) {
            $reduction = Reduction::where('REDUC_CODE', $reductionCode)->first();
            if ($reduction) {
                $price = round($price * (1 - $reduction->REDUC_VALUE / 100), 2);
            }
        }

        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_BASE_URL', 'https://api-m.paypal.com');

        if (!$clientId || !$secret) {
            Log::error('PAYPAL_CLIENT_ID ou PAYPAL_SECRET manquant côté serveur.');
            return [
                'success' => false,
                'error' => 'Configuration serveur incomplète',
                'http_code' => 500,
            ];
        }

        // Étape 1 : récupérer le token d'accès
        $tokenResponse = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("$baseUrl/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($tokenResponse->failed()) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération du token',
                'http_code' => $tokenResponse->status(),
            ];
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // Étape 2 : créer la commande, avec le prix calculé par le serveur
        $orderResponse = Http::withToken($accessToken)
            ->post("$baseUrl/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => number_format($price, 2, '.', ''),
                        ]
                    ]
                ]
            ]);

        if ($orderResponse->failed()) {
            return [
                'success' => false,
                'error' => 'Erreur PayPal #: ' . $orderResponse->status(),
                'http_code' => $orderResponse->status(),
            ];
        }

        $result = $orderResponse->json();

        PaymentOrder::create([
            'order_id' => $result['id'],
            'email' => $email,
            'days' => $days,
            'price' => $price,
            'status' => 'created',
        ]);

        $result['access_token'] = $accessToken;
        $result['success'] = true;
        $result['http_code'] = 200;

        return $result;
    }
}
