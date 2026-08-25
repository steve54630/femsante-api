<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API only : un utilisateur non authentifie n'est jamais redirige vers une route
        // "login" (inexistante ici). Evite RouteNotFoundException dans le middleware
        // auth:sanctum quand la requete n'envoie pas Accept: application/json.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API only : une requete non authentifiee renvoie toujours un 401 JSON, jamais
        // une redirection vers une route "login" (qui n'existe pas ici et provoquait
        // RouteNotFoundException sur les routes protegees par auth:sanctum).
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'error'   => 'Non authentifié',
            ], 401);
        });
    })->create();
