<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp;

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/registration', function (Request $request, Response $response, array $args) {
    $appSecret = 'sw-example-app-1337';
    $appName = 'SwExampleApp';

    $queryString = $request->getUri()->getQuery();
    $signature = hash_hmac('sha256', $queryString, $appSecret);

    # TODO Verify signature

    parse_str($queryString, $queryValues);
    $proof = \hash_hmac(
        'sha256',
        $queryValues['shop-id'] . $queryValues['shop-url'] . $appName,
        $appSecret
    );

    $secret = bin2hex(random_bytes(32));

    # TODO Store information

    $response->getBody()->write(
        json_encode([
            'proof' => $proof,
            'secret' => $secret,
            'confirmation_url' => sprintf('%s/registration/confirm', $_SERVER['BACKEND_URL']),
        ])
    );

    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/registration/confirm', function (Request $request, Response $response, array $args) {
    # TODO Verify Signature

    $data = $request->getParsedBody();

    file_put_contents(
        sprintf(__DIR__ . '/../shops/%s.json', $data['shopId']),
        json_encode($data, JSON_THROW_ON_ERROR)
    );

    return $response->withStatus(200);
});

$app->post('/customer/greet', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    # TODO Send out a nice greeting to the customer

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/greetings/module', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/module.html'));

    return $response;
});

$app->run();
