<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp;

require_once __DIR__ . '/../vendor/autoload.php';

use Fschmtt\SwExampleApp\Action\Confirm;
use Fschmtt\SwExampleApp\Action\Greet;
use Fschmtt\SwExampleApp\Action\Register;
use Fschmtt\SwExampleApp\Middleware\MetaInformation;
use Fschmtt\SwExampleApp\Middleware\ShopwareVersion;
use Fschmtt\SwExampleApp\Middleware\VerifyAppSignature;
use Fschmtt\SwExampleApp\Middleware\VerifyShopSignature;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->add(new ShopwareVersion());

$app->get('/registration', new Register())
    ->add(new VerifyAppSignature());

$app->post('/registration/confirm', new Confirm())
    /*->add(new VerifyShopSignature())
    ->add(new MetaInformation())*/;

$app->post('/customer/greet', new Greet())
    ->add(new MetaInformation())
    ->add(new VerifyShopSignature());

$app->get('/greetings/module', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/module.html'));

    return $response;
})->add(new VerifyShopSignature());

$app->run();
