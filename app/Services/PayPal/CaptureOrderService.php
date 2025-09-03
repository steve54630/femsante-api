<?php

namespace App\Services\Paypal;

use Illuminate\Support\Facades\Http;
use App\Http\Request\PayPal\CaptureOrderRequest;

class CaptureOrderService
{
    public function __invoke(CaptureOrderRequest $request): array
    {
        $orderid = $request->input('orderId');
        $accessToken = $request->input('accessToken');

        $captureResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post(env('PAYPAL_BASE_URL', 'https://api-m.paypal.com') . "/v2/checkout/orders/$orderid/capture", []);

        if ($captureResponse->failed()) {
            return [
                'success' => false,
                'error' => 'Erreur PayPal #: ' . $captureResponse->status(),
                'http_code' => $captureResponse->status(),
            ];
        }

        $result = $captureResponse->json();
        $captureStatus = $result['purchase_units'][0]['payments']['captures'][0]['status'] ?? null;

        return match($captureStatus) {
            'COMPLETED' => ['success' => true, 'http_code' => 200] + $result,
            'DECLINED' => ['success' => false, 'http_code' => 400, 'error' => 'Payment annulé par l\'utilisateur'] + $result,
            default => ['success' => false, 'http_code' => 500, 'error' => 'Prenez contact avec Paypal pour l\'erreur'] + $result,
        };
    }
}
