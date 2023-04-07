<?php
session_start();
date_default_timezone_set('Africa/Lagos');

use MultiveLogger\LoggerFactory;
use DI\ContainerBuilder;
use Monolog\Logger;
use MultiveLogger\LoggerNewAccount;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as ResponseThis;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;
use DI\Bridge\Slim\Bridge as SlimAppFactory;

$settings = require __DIR__ . '/app/settings.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/helpers/cookie.php';

const REMOTE_ADDR = ['192.168.43.8', 'localhost', '127.0.0.1', '192.168.43.237', '::1'];
$isLiveServer = false;
if (!in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $isLiveServer = true;
    //die($_SERVER['REMOTE_ADDR']);
} else {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.example');
}
$dotenv->load();
try {
    $dotenv->required(['DB_HOST', 'DB_NAME', 'USER', 'PASSWORD', 'JAVASCRIPT_VERSION_CONTROL', 'PROJECT_WEB_DOMAIN_URL']);
    if ($isLiveServer) {
        $dotenv->required(['DB_HOST', 'DB_NAME', 'USER', 'PASSWORD', 'JAVASCRIPT_VERSION_CONTROL', 'PROJECT_WEB_DOMAIN_URL'])->notEmpty();
    } else {
        $dotenv->required(['DB_HOST', 'DB_NAME', 'USER', 'JAVASCRIPT_VERSION_CONTROL', 'PROJECT_WEB_DOMAIN_URL'])->notEmpty();
    }
    $dotenv->required('JAVASCRIPT_VERSION_CONTROL')->isInteger();
} catch (RuntimeException $e) {
    die('dotenv file :: ' .$e->getMessage());
}

$containerBuilder = new ContainerBuilder();
/** @noinspection PhpUnhandledExceptionInspection */
$container = $containerBuilder->build();
$container->set('upload_directory', __DIR__ . '/storage'. DIRECTORY_SEPARATOR);
// e.g $path = $this->get('upload_directory');

$settings($container);
try {
    $dbSettings = $container->get('dbSettings')['db'];
    $capsule = new Capsule;
    $capsule->addConnection($dbSettings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
    echo 'error';
}

const LOGGER_SETTINGS = [
    'name' => 'app',
    'path' => __DIR__ . DIRECTORY_SEPARATOR. 'logs',
    'filename' => 'app.log',
    'level' => Logger::DEBUG,
    'file_permission' => 0775,
];
$container->set('MultiveErrorLoggerFactory', function() : LoggerFactory {
    return new LoggerFactory(LOGGER_SETTINGS);
});
$container->set('LoggerNewAccount', function() : LoggerNewAccount {
    return new LoggerNewAccount(new LoggerFactory(LOGGER_SETTINGS));
});
/*
 * USAGE
 * $newUser = new \MultiveLogger\models\UserModel();
 * $newUser->setName(234);
 * $this->get('LoggerNewAccount')->registerUser($newUser);
 */

// Create App
$app = SlimAppFactory::create($container);
$app->addBodyParsingMiddleware();
$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseThis {
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

    $response = $handler->handle($request);
    $response = $response->withHeader('Access-Control-Allow-Origin', $_ENV['ACCESS_CONTROL']);
    $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
    // Optional: Allow Ajax CORS requests with Authorization header
    return $response->withHeader('Access-Control-Allow-Credentials', 'true');
});
$app->addRoutingMiddleware();
$app->setBasePath(getBasePath());
if (!$isLiveServer) {
    try {
        $MultiveErrorLoggerFactory = $app->getContainer()->get('MultiveErrorLoggerFactory')->addFileHandler('error.log')->createLogger();
        $app->addErrorMiddleware(true, true, true, $MultiveErrorLoggerFactory);
        error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
        ini_set('ignore_repeated_errors', TRUE); // always use TRUE
        ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
        ini_set('log_errors', TRUE); // Error/Exception file logging engine.
        ini_set('error_log',  __DIR__ . '/cache/errors.log'); // Logging file path
    } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {}
}

// views
$webRoutes = require __DIR__ . '/routes/web.php';
$webRoutes($app);
// servers
$apiRoutes = require __DIR__ . '/routes/api.php';
$apiRoutes($app);

// HttpNotFound Middleware
$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    try {
        return $handler->handle($request);
    } catch (HttpNotFoundException) {
        $response = (new Response())->withStatus(404);
        return (new PhpRenderer(__DIR__ . '/views/'))
            ->render($response, "errorPage.php");
    }
});
// end

// route caching
if ($isLiveServer) {
    $routeCollector = $app->getRouteCollector();
    $cacheFile = __DIR__ . '/storage/cache/route_cache.php';
    $routeCollector->setCacheFile($cacheFile);
}
// end
$app->run();
