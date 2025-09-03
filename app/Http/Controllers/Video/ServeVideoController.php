<?php

namespace App\Http\Controllers\Video;

use App\Http\Request\Video\ServeVideoRequest;
use App\Services\Video\ServeVideoService;

class ServeVideoController {

    private ServeVideoService $service;

    public function __construct(ServeVideoService $service) {
        $this->service = $service;
    }

    public function __invoke(ServeVideoRequest $request) {
        try {
            $content = $this->service->__invoke(
                $request->titre(),
                $request->video(),
                $request->type()
            );

            return response($content, 200)
                ->header('Content-Type', 'application/vnd.apple.mpegurl');
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}