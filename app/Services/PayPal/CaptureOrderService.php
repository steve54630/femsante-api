<?php

namespace App\Services\Paypal;

use App\Http\Request\PayPal\CaptureOrderRequest;
use App\Models\PaymentOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? null;
        $captureStatus = $capture['status'] ?? null;

        if ($captureStatus !== 'COMPLETED') {
            return match($captureStatus) {
                'DECLINED' => ['success' => false, 'http_code' => 400, 'error' => 'Payment annulé par l\'utilisatrice'] + $result,
                default => ['success' => false, 'http_code' => 500, 'error' => 'Prenez contact avec Paypal pour l\'erreur'] + $result,
            };
        }

        $order = PaymentOrder::where('order_id', $orderid)->first();

        if (!$order) {
            // Ne devrait jamais arriver (la commande est créée par notre propre
            // create-order) — refusé plutôt que d'accorder un accès non traçable.
            Log::error("PaymentOrder introuvable pour order_id=$orderid après capture PayPal réussie.");
            return [
                'success' => false,
                'error' => 'Commande introuvable côté serveur',
                'http_code' => 500,
            ];
        }

        $capturedAmount = (float) ($capture['amount']['value'] ?? 0);

        if (abs($capturedAmount - (float) $order->price) > 0.01) {
            Log::error("Montant capturé ($capturedAmount) différent du montant attendu ({$order->price}) pour order_id=$orderid.");
            return [
                'success' => false,
                'error' => 'Incohérence de montant détectée',
                'http_code' => 500,
            ];
        }

        $order->update([
            'captured_amount' => $capturedAmount,
            'status' => 'captured',
        ]);

        return ['success' => true, 'http_code' => 200] + $result;
    }
}
