<?php

namespace App\Http\Controllers\User;

use App\Http\Request\User\FreeTrialRequest;
use App\Services\User\FreeTrialService;

class FreeTrialController
{
    private FreeTrialService $service;

    public function __construct(FreeTrialService $service)
    {
        $this->service = $service;
    }

    public function __invoke(FreeTrialRequest $request)
    {
        $result = $this->service->__invoke($request);
        return response()->json($result, $result['http_code']);
    }
}
