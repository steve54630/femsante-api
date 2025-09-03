<?php

namespace App\Http\Controllers\User;

use App\Services\User\RegisterUserService;
use App\Http\Request\User\RegisterUserRequest;

class RegisterUserController
{
    private RegisterUserService $service;

    public function __construct(RegisterUserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(RegisterUserRequest $request)
    {
        $result = $this->service->__invoke($request);

        // Retourne une réponse JSON propre
        return response()->json($result, $result['http_code']);
    }
}
