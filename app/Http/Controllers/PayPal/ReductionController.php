<?php

namespace App\Http\Controllers\PayPal;

use App\Services\PayPal\ReductionService;
use App\Http\Request\PayPal\ReductionRequest;

class ReductionController {
    private ReductionService $service;

    public function __construct(ReductionService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ReductionRequest $request)
    {
        return $this->service->__invoke($request);
    }
}
