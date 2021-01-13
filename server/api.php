<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext as GetRoute;
use Slim\Psr7\Response as Res;
use Slim\App;

return function (App $app) {

    $apiBase = $_ENV['SERVER_API_ROUTE_BASE'];

    $verifyAccountMiddleware = function (Request $request, RequestHandler $handler) {
        $verify = new DB();
        $newResponse = new Res();
        if ($request->getMethod() === 'GET') { //get request
            /** @noinspection NullPointerExceptionInspection */
            $routeArguments = GetRoute::fromRequest($request)->getRoute()->getArguments();
            $userId = ($routeArguments['userId'] ?? '');
            $token = ($routeArguments['token'] ?? '');
        } else { //post request
            $userId = ($request->getParsedBody()['userId'] ?? '');
            $token = ($request->getParsedBody()['token'] ?? '');
        }
        if (!empty($userId) && !empty($token)) {
            if ($verify->verifyUser($userId, $token)) {
                $response = $handler->handle($request);
                $routeResponse = (string)$response->getBody();
                $decodeJson = json_decode($routeResponse, true, 512, JSON_THROW_ON_ERROR);
                return returnMyStatus($decodeJson, $newResponse);
            }

            $verify->response['statusStr'] = 'Invalid access';
            return returnMyStatus($verify->response, $newResponse);
        }

        $verify->response['statusStr'] = 'UserId and token is empty';
        return returnMyStatus($verify->response, $newResponse);
    };

    $tokenBasedAuthMiddleware = function (Request $request, RequestHandler $handler) {
        $newResponse = new Res();
        $verify = new DB();
        $bearerToken = TokenGenerator::getBearerToken($request->getHeaders()['Authorization'][0] ?? ''); // token
        if (TokenGenerator::validateToken($bearerToken)) {
            $tokenPayload = TokenGenerator::getPayload($bearerToken);
            $response = $handler->handle($request->withAttribute('tokenPayload', $tokenPayload));
            $routeResponse = $response->getBody();
            $verify->response = json_decode($routeResponse, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $verify->response['statusStr'] = 'Invalid token';
        }
        return returnMyStatus($verify->response, $newResponse);
    };

    $app->post($apiBase.'/registration', function (Request $request, Response $response) {
        require_once __DIR__ . '/../controllers/REGISTRATION.php';
        $hello = new REGISTRATION();
        $response->getBody()->write($hello->helloWorld());
        return $response;
    });

    $app->get($apiBase.'/hello', function (Request $request, Response $response) {
        require_once __DIR__ . '/../controllers/REGISTRATION.php';
        $array = array();
        $array['status'] = true;
        return returnMyStatus($array, $response);
    });
};
