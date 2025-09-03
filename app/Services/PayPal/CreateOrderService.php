<?php

namespace App\Services\Paypal;

use Illuminate\Support\Facades\Http;
use App\Http\Request\PayPal\CreateOrderRequest;

class CreateOrderService
{
    public function __invoke(CreateOrderRequest $request): array
    {
        $price = $request->input('price');
        $clientId = $request->input('clientId');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_BASE_URL', 'https://api-m.paypal.com');

        if (!$price) {
            return [
                'success' => false,
                'error' => 'Prix manquant',
                'http_code' => 400,
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

        // Étape 2 : créer la commande
        $orderResponse = Http::withToken($accessToken)
            ->post("$baseUrl/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => $price,
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
        $result['access_token'] = $accessToken;
        $result['success'] = true;
        $result['http_code'] = 200;

        return $result;
    }
}
