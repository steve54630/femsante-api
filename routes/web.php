<?php

use Illuminate\Support\Facades\Route;

// Si jamais ce n'est pas une route API, redirection
Route::fallback(function () {
    return redirect('https://www.audreyretournay-dieteticiennenutritionniste.com/femsante-p360447.html', 301);
});
