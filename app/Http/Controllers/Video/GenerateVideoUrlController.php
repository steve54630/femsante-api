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
        // L'authentification est desormais assuree par le middleware auth:sanctum ;
        // le controleur n'a plus a verifier le token lui-meme.
        $video = $request->query('video');

        $result = ($this->service)($video);

        return response()->json($result, $result['http_code']);
    }
}
