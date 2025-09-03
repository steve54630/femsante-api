<?php

namespace App\Http\Controllers\User;

use App\Services\User\ForgottenPasswordService;
use App\Http\Request\User\ForgottenPasswordRequest;

class ForgottenPasswordController {

    private ForgottenPasswordService $service;

    public function __construct(ForgottenPasswordService $service) {
        $this->service = $service;
    }

    public function __invoke(ForgottenPasswordRequest $request) {
        $result = $this->service->__invoke($request);

        // Retourne une réponse JSON propre
        return response()->json($result, $result['http_code']);
    }
}