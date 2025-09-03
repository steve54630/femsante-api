<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'error' => 'Endpoint API invalide.'
    ], 404);
});
