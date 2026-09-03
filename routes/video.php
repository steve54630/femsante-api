<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Video\{
    GenerateVideoUrlController,
    ServeVideoController
};

// generate-url exige un token utilisateur (Sanctum). /serve reste protege par le HMAC
// present dans l'URL, pour que le lecteur (ExoPlayer) puisse charger le flux sans en-tete.
Route::middleware('auth:sanctum')->get('/generate-url', GenerateVideoUrlController::class);
Route::get('/serve', ServeVideoController::class);
