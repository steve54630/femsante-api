<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\{
    RegisterUserController,
    ForgottenPasswordController,
    ConnectUserController,
    UpdateUserController,
    CheckEmailController,
    FreeTrialController
};

Route::post('/register', RegisterUserController::class);
Route::post('/forgotten-password', ForgottenPasswordController::class);
Route::post('/connect', ConnectUserController::class);
Route::post('/update', UpdateUserController::class);
Route::post('/check-email', CheckEmailController::class);
Route::post('/free-trial', FreeTrialController::class);
