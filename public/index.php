<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp;

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

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
            'confirmation_url' => 'http://sw-example-app/registration/confirm'
        ])
    );

    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->post('/registration/confirm', function (Request $request, Response $response, array $args) {
    # TODO Verify Signature

    $data = $request->getParsedBody();
    # TODO Store information

    return $response->withStatus(200);
});

$app->run();