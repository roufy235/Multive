<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext as GetRoute;
use Slim\Psr7\Response as Res;
use Slim\App;


function returnMyStatus (array $responseArray, Response $response): Response {
    try {
        $response->getBody()->write(json_encode($responseArray, JSON_THROW_ON_ERROR));
        if ($responseArray['status']) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        return $response->withHeader('Content-Type', 'application/json')->withStatus(202);
    } catch (JsonException $e) {
        return $response->withHeader('Content-Type', 'application/json')->withStatus(205);
    }
}

return function (App $app) {

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

    $app->post('/api/registration', function (Request $request, Response $response) {
        require_once __DIR__ . '/../controllers/REGISTRATION.php';
        $hello = new REGISTRATION();
        $response->getBody()->write($hello->helloWorld());
        return $response;
    });
};
