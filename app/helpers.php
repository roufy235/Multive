<?php

use Carbon\Factory;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;


/** @noinspection PhpUnhandledExceptionInspection */
function view(ResponseInterface $response, string $template, array $with) : ResponseInterface{
    return (new PhpRenderer(__DIR__ . '/../resources/views'))
        ->render($response, $template, $with);
}
