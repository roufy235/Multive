<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

use Slim\App;
use Slim\Views\PhpRenderer;

return function (App $app) {

    $app->get('/', function ($request, $response, $args) {
        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, "index.php", [

        ]);
    });

    $app->get('/hello/{name}', function ($request, $response, $args) {
        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, "name.php", ['name' => $args['name'],]);
    });


};
