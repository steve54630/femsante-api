<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Charger le fichier de maintenance si besoin
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload Composer
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap de l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var Application $app */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Capture la requête HTTP
$request = Request::capture();

// Traitement de la requête et génération de la réponse
$response = $kernel->handle($request);

// Retour de la réponse (toujours JSON dans le cas d'une API)
$response->send();

// Terminer le kernel
$kernel->terminate($request, $response);
