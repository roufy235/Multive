<?php
use Psr\Http\Message\ResponseInterface as ResponseThis;

function getBasePath(bool $isApi = false) : string { // project base path
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $uri = (string) parse_url('http://a' . $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
        if ($isApi) { //calling for api base path
            return $scriptDir.$_ENV['SERVER_API_ROUTE_BASE'];
        }
        return $scriptDir;
    }
    if ($isApi) {
        return $_ENV['SERVER_API_ROUTE_BASE'];
    }
    return '';
}

function getAdminUrlBasePath() : string { // project base path
    return getBasePath().$_ENV['ADMIN_ROUTE_BASE'];
}


/** @noinspection PhpMultipleClassDeclarationsInspection */
function returnMyStatus (array $responseArray, ResponseThis $response): ResponseThis {
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
