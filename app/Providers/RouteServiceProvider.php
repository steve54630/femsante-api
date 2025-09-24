<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        Log::info('AppServiceProvider boot');
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/user')
                ->group(base_path('routes/user.php'));

            Route::middleware('api')
                ->prefix('api/paypal')
                ->group(base_path('routes/paypal.php'));

            Route::middleware('api')
                ->prefix('api/video')
                ->group(base_path('routes/video.php'));

            // Si tu veux garder les classiques
            Route::middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}