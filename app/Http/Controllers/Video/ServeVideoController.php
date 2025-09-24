<?php

namespace App\Http\Controllers\Video;

use App\Http\Request\Video\ServeVideoRequest;
use App\Services\Video\ServeVideoService;
use Illuminate\Support\Facades\Log;

class ServeVideoController
{

    private ServeVideoService $service;

    public function __construct(ServeVideoService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServeVideoRequest $request)
    {
        try {
            
            Log::info('Request: ' . json_encode($request->all()));
            
                $content = $this->service->__invoke(
                    $request->titre(),
                    $request->video(),
                    $request->type(),
                    $request->videoSegment()
                );

                if($request->has('segment')){
                    return response()->file($content);
                }

                return response($content, 200)
                    ->header('Content-Type', 'application/vnd.apple.mpegurl');
            
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
