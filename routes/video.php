<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Video\{
    GenerateVideoUrlController,
    ServeVideoController
};

Route::get('/generate-url', GenerateVideoUrlController::class);
Route::get('/serve', ServeVideoController::class);
