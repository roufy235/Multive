<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

use App\Http\Controllers\WelcomeController;
use Slim\App;

return function (App $app) {

    $app->get('/welcome', [WelcomeController::class, 'index']);
    $app->get('/welcome/{name}', [WelcomeController::class, 'show']);

};