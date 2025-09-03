<?php

namespace App\Http\Controllers\User;

use App\Http\Request\User\ConnectUserRequest; // FormRequest pour validation
use App\Services\User\ConnectUserService;     // Service contenant la logique métier

class ConnectUserController
{
    protected ConnectUserService $service;

    public function __construct(ConnectUserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ConnectUserRequest $request)
    {
        // On délègue toute la logique métier au service
        $result = $this->service->__invoke($request);

        // Retourne une réponse JSON propre
        return response()->json($result, $result['http_code']);
    }
}
