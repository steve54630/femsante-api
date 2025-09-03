<?php

namespace App\Http\Controllers\Video;

use Illuminate\Http\Request;
use App\Services\Video\GenerateVideoUrlService;

class GenerateVideoUrlController
{
    private GenerateVideoUrlService $service;

    public function __construct(GenerateVideoUrlService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request)
    {
        $video = $request->query('video');
        $authHeader = $request->header('Authorization');

        $result = ($this->service)($video, $authHeader);

        return response()->json($result, $result['http_code']);
    }
}
