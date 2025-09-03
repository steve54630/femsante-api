<?php

namespace App\Services\Paypal;

use App\Http\Request\PayPal\ReductionRequest;
use App\Models\Reduction;

class ReductionService
{
    public function __invoke(ReductionRequest $request)
    {
        $reducCode = $request->input('reductionCode');

        $reduction = Reduction::where('REDUC_CODE', $reducCode)->first();

        if (!$reduction) {
            return [
                'success' => false,
                'error' => "Le code de réduction saisi n'existe pas.",
                'http_code' => 404,
            ];
        }

        return [
            'success' => true,
            'reduction' => $reduction->REDUC_VALUE,
            'http_code' => 200,
        ];
    }
}
