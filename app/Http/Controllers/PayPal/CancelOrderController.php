<?php

namespace App\Http\Controllers\PayPal;

use App\Services\PayPal\CancelOrderService;
use App\Http\Request\PayPal\CancelOrderRequest;

class CancelOrderController
{
    private CancelOrderService $service;

    public function __construct(CancelOrderService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CancelOrderRequest $request)
    {
        $data = $this->service->__invoke($request);
        return response()->json($data, $data['http_code']);
    }
}
