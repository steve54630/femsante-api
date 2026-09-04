<?php

namespace App\Services\PayPal;

use App\Http\Request\PayPal\CancelOrderRequest;
use App\Models\PaymentOrder;

class CancelOrderService
{
    public function __invoke(CancelOrderRequest $request): array
    {
        $orderId = $request->input('orderId');

        $order = PaymentOrder::where('order_id', $orderId)->first();

        if (!$order) {
            return [
                'success' => false,
                'error' => 'Commande introuvable',
                'http_code' => 404,
            ];
        }

        // Ne jamais écraser un paiement déjà capturé/consommé : seule une commande
        // encore "created" (jamais approuvée côté PayPal) peut être marquée annulée.
        if ($order->status === 'created') {
            $order->update(['status' => 'cancelled']);
        }

        return [
            'success' => true,
            'http_code' => 200,
        ];
    }
}
