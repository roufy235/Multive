<?php
session_start();
date_default_timezone_set('Africa/Lagos');

use MultiveLogger\LoggerFactory;
use DI\ContainerBuilder;
use Medoo\Medoo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as ResponseThis;
use Slim\Routing\RouteContext;
require __DIR__ . '/vendor/autoload.php';

const REMOTE_ADDR = ['192.168.43.8', 'localhost', '127.0.0.1', '192.168.43.237'];
$isLiveServer = false;
if (!in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $isLiveServer = true;
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

require __DIR__ . '/helpers/cookie.php';

$containerBuilder = new ContainerBuilder();
/** @noinspection PhpUnhandledExceptionInspection */
$container = $containerBuilder->build();
$container->set('upload_directory', __DIR__ . '/uploads'. DIRECTORY_SEPARATOR);
// e.g $path = $this->get('upload_directory');

const LOGGER_SETTINGS = [
    'name' => 'app',
    'path' => __DIR__ . DIRECTORY_SEPARATOR. 'logs',
    'filename' => 'app.log',
    'level' => Logger::DEBUG,
    'file_permission' => 0775,
];

$container->set('MultiveLoggerFactory', function() : LoggerFactory {
    return new LoggerFactory(LOGGER_SETTINGS);
});
// e.g $this->get('MultiveLoggerFactory')->error('Bar', ['hello', __FILE__, __LINE__]);


$container->set('logger', function() : Logger {
    $name = $_ENV['PROJECT_NAME'];
    $logger = new Logger($name);
    $file_handler = new StreamHandler(__DIR__ . '/logs/'. $name .'.log', Logger::WARNING);
    $logger->pushHandler($file_handler);
    return $logger;
});
// e.g $this->get('logger')->error('Bar', ['hello', __FILE__, __LINE__]);

$container->set('databaseConnection', function() : array {
    return [
        'database_type' => 'mysql',
        'database_name' => $_ENV['DB_NAME'],
        'server' => 'localhost',
        'username' => $_ENV['USER'],
        'password' => $_ENV['PASSWORD']
    ];
});

AppFactory::setContainer($container);

// Create App
$app = AppFactory::create();
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
    $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
    return $response;
});
$app->addRoutingMiddleware();
$app->setBasePath(getBasePath());
if (in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    $logger = $app->getContainer()->get('MultiveLoggerFactory')->addFileHandler('error.log')->createLogger();
    $app->addErrorMiddleware(true, true, true, $logger);
    error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

    ini_set('ignore_repeated_errors', TRUE); // always use TRUE

    ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

    ini_set('log_errors', TRUE); // Error/Exception file logging engine.
    ini_set('error_log',  __DIR__ . '/cache/errors.log'); // Logging file path
}

// views
$routes = require __DIR__ . '/router/route.php';
$routes($app);

// servers
$routesApi = require __DIR__ . '/server/api.php';
$routesApi($app);



// HttpNotFound Middleware
$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
    try {
        return $handler->handle($request);
    } catch (HttpNotFoundException $httpException) {
        $response = (new Response())->withStatus(404);
        $renderer = new PhpRenderer(__DIR__ . '/views/');
        return $renderer->render($response, "errorPage.php", [

        ]);
    }
});
// end

// route caching
//$routeCollector = $app->getRouteCollector();
//$cacheFile = __DIR__ . '/cache/cache.php';
//$routeCollector->setCacheFile($cacheFile);
// end
$app->run();
