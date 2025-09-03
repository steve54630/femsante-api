<?php

namespace App\Http\Controllers\User;

use App\Http\Request\User\CheckEmailRequest;
use App\Services\User\CheckEmailService;

class CheckEmailController
{
    private CheckEmailService $service;

    public function __construct(CheckEmailService $service)
    {
        $this->service = $service;
    }

    public function __invoke(CheckEmailRequest $request)
    {
        $result = $this->service->__invoke($request->email());

        return response()->json($result, $result['http_code']);
    }
}
