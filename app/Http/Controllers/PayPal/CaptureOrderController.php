<?php

namespace App\Http\Controllers\Paypal;

use App\Services\Paypal\CaptureOrderService;
use App\Http\Request\PayPal\CaptureOrderRequest;

class CaptureOrderController
{
    private CaptureOrderService $service;

    public function __construct(CaptureOrderService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CaptureOrderRequest $request)
    {
        $data = $this->service->__invoke($request);
        return response()->json($data, $data['http_code']);
    }
}
