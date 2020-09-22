<?php
session_start();
date_default_timezone_set('Africa/Lagos');
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as ResponseThis;
use Slim\Routing\RouteContext;

function getBasePath(bool $isApi = false) : string { // project base path
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $uri = (string) parse_url('http://a' . $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
        if ($isApi) { //calling for api base path
            return $scriptDir.'/api';
        }
        return $scriptDir;
    }
    return '';
}

const REMOTE_ADDR = array('192.168.43.8', 'localhost', '127.0.0.1', '192.168.43.166', '192.168.43.237');
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/access_file.php';
require __DIR__ . '/controllers/DB.php';
require __DIR__ . '/helpers/myFunctions.php';
require __DIR__ . '/helpers/cookie.php';

// Create App
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseThis {
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

    $response = $handler->handle($request);
    $response = $response->withHeader('Access-Control-Allow-Origin', ACCESS_CONTROL);
    $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
    // Optional: Allow Ajax CORS requests with Authorization header
    $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
    return $response;
});
$app->addRoutingMiddleware();
$app->setBasePath(getBasePath());
if (in_array($_SERVER['REMOTE_ADDR'], REMOTE_ADDR)) {
    $app->addErrorMiddleware(true, true, true);
}


// view
$routes = require __DIR__ . '/router/route.php';
$routes($app);

// server
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
