<?php

return function (\Psr\Container\ContainerInterface $container) {
    $container->set('dbSettings', function () {
        return [
            // Slim Settings
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'db' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => $_ENV['DB_NAME'],
                'username' => $_ENV['USER'],
                'password' => $_ENV['PASSWORD'],
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        ];
    });
};
