<?php

namespace App\Http\Controllers\User;

use App\Http\Request\User\UpdateUserRequest;
use App\Services\User\UpdateUserService;

class UpdateUserController 
{
    private UpdateUserService $service;

    public function __construct(UpdateUserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(UpdateUserRequest $request)
    {
        $result = $this->service->__invoke($request);
        return response()->json($result);
    }
}
