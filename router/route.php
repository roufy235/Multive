<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

use MultiveLogger\models\UserModel;
use ReallySimpleJWT\Token;
use Slim\App;
use Slim\Views\PhpRenderer;

return function (App $app) {

    $app->get('/', function ($request, $response, $args) {

        $newUser = new UserModel();
        $newUser->setName(234);
        //$this->get('LoggerNewAccount')->registerUser($newUser);

        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, "index.php", [

        ]);
    });

    $app->get('/database', function ($request, $response, $args) {
        //https://medoo.in/doc
        $database = new \Medoo\Medoo($this->get('databaseConnection'));
        $selectAll = $database->select('users', '*');
        echo '<pre>';
        print_r($selectAll);
        die();

        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, "index.php", [

        ]);
    });

    $app->get('/testVueThree', function ($request, $response, $args) {
        $renderer = new PhpRenderer(__DIR__ . '/../views/');
        return $renderer->render($response, "vueThreeTest.php", [

        ]);
    });

    $app->get('/hello/{name}', function ($request, $response, $args) {
        $renderer = new PhpRenderer(__DIR__ . '/../views/');

        $userId = [
            'username' => 'ertyuiop',
            'surname' => 'hfhfhfhfhfh'
        ];
        $secret = 'sec!ReT423*&';
        $expiration = time() + 3600;
        $issuer = 'localhost';

        $token = Token::create($userId, $secret, $expiration, $issuer);
        $result = Token::validate($token, $secret);
        $payload = Token::getPayload($token, $secret);
        $header = Token::getHeader($token, $secret);


        return $renderer->render($response, "name.php", [
            'name' => $args['name'],
            'token' => $token,
            'result' => $result,
            'payload' => $payload,
            'header' => $header,
            ]);
    });


};
