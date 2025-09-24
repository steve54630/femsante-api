<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Si jamais ce n'est pas une route API, redirection
Route::fallback(function () {
    $currentUrl = request()->fullUrl();
    Log::info("Route API invalide : $currentUrl");
    return redirect('https://www.audreyretournay-dieteticiennenutritionniste.com/femsante-p360447.html', 301);
});
