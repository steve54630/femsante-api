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

        $order = PaymentOrder::where('order_id', $orderid)->first();

        // Idempotence : un intent=CAPTURE PayPal ne peut être capturé qu'une seule fois. Un
        // appel en double (retry réseau côté app, double-tap, etc.) sur une commande déjà
        // capturée ne doit jamais retaper PayPal — juste confirmer ce qu'on a déjà en base,
        // sinon l'app reçoit un refus PayPal ("Order already captured") alors que le paiement
        // a réellement réussi la première fois.
        if ($order && in_array($order->status, ['captured', 'consumed'], true)) {
            Log::info("Capture idempotente : order_id=$orderid déjà status={$order->status}, PayPal non re-sollicité.");
            return ['success' => true, 'http_code' => 200];
        }

        // Http::post($url, []) avec un header Content-Type forcé à la main n'envoie pas un
        // corps JSON pour autant (Laravel encode [] en form-urlencoded par défaut) : le corps
        // part vide alors que l'en-tête annonce du JSON, ce que PayPal rejette en
        // MALFORMED_REQUEST_JSON. withBody('{}', ...) envoie un corps réellement JSON, conforme
        // à l'exemple officiel PayPal pour cet endpoint (qui n'attend pas de payload).
        $captureResponse = Http::withToken($accessToken)
            ->withBody('{}', 'application/json')
            ->post(env('PAYPAL_BASE_URL', 'https://api-m.paypal.com') . "/v2/checkout/orders/$orderid/capture");

        if ($captureResponse->failed()) {
            Log::error("Échec capture PayPal pour order_id=$orderid (HTTP {$captureResponse->status()}) : " . $captureResponse->body());

            $body = $captureResponse->json();
            $detail = $body['details'][0]['description'] ?? $body['message'] ?? null;

            return [
                'success' => false,
                'error' => $detail ?? ('Erreur PayPal #: ' . $captureResponse->status()),
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
