<?php

namespace App\Http\Controllers\Paypal;

use App\Services\Paypal\CreateOrderService;
use App\Http\Request\PayPal\CreateOrderRequest;

class CreateOrderController
{
    private CreateOrderService $service;

    public function __construct(CreateOrderService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CreateOrderRequest $request)
    {
        $data = $this->service->__invoke($request);
        return response()->json($data, $data['http_code']);
    }
}
