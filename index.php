<?php
session_start();
date_default_timezone_set('Africa/Lagos');
use Slim\Factory\AppFactory;

const REMOTE_ADDR = array('192.168.43.8', 'localhost', '127.0.0.1', '192.168.43.166', '192.168.43.237');
// die($_SERVER['REMOTE_ADDR']);
if (!in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    require __DIR__ . '/controllers/access/Online.php';
    //die($_SERVER['REMOTE_ADDR']);
} else {
    require __DIR__ . '/controllers/access/LocalAccess.php';
}


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/access_file.php';
require_once __DIR__ . '/controllers/SQL.php';
require __DIR__ . '/controllers/DB.php';

// Create App
$app = AppFactory::create();
header('Access-Control-Allow-Origin:'.ACCESS_CONTROL);
header('Access-Control-Allow-Headers:X-Request-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->setBasePath(BASE_PATH);
if (in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    $app->addErrorMiddleware(true, true, true);
}

$routes = require __DIR__ . '/routes/route.php';
$routesApi = require __DIR__ . '/routes/api.php';
$routes($app);
$routesApi($app);
$app->run();
