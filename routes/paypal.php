<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPal\{
    CreateOrderController,
    CaptureOrderController,
    ReductionController
};

Route::post('/create-order', CreateOrderController::class);
Route::post('/capture-order', CaptureOrderController::class);
Route::post('/reduction', ReductionController::class);
