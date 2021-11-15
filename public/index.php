<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp;

require_once __DIR__ . '/../vendor/autoload.php';

use Fschmtt\SwExampleApp\ActionButton\GreetCustomer as ActionButtonGreetCustomer;
use Fschmtt\SwExampleApp\Middleware\ShopwareVersion;
use Fschmtt\SwExampleApp\Middleware\VerifyAppSignature;
use Fschmtt\SwExampleApp\Middleware\VerifyShopSignature;
use Fschmtt\SwExampleApp\Registration\Confirm;
use Fschmtt\SwExampleApp\Registration\Register;
use Fschmtt\SwExampleApp\Webhook\GreetCustomer as WebhookGreetCustomer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->add(new ShopwareVersion());

/**
 * Registrations
 */
$app->group('/registration', function (RouteCollectorProxy $app) {
    $app->get('/register', new Register())
        ->add(new VerifyAppSignature());

    $app->post('/confirm', new Confirm())
        ->add(new VerifyShopSignature());
});

/**
 * Modules
 */
$app->group('/modules', function (RouteCollectorProxy $app) {
    $app->get('/greetings', function (Request $request, Response $response, array $args) {
        $response->getBody()->write(file_get_contents(__DIR__ . '/module.html'));

        return $response;
    });
})->add(new VerifyShopSignature());

/**
 * Webhooks
 */
$app->group('/webhooks', function (RouteCollectorProxy $app) {
    $app->post('/greet-customer', new WebhookGreetCustomer());
})->add(new VerifyShopSignature());

/**
 * Action buttons
 */
$app->group('/action-buttons', function (RouteCollectorProxy $app) {
    $app->post('/greet-customer', new ActionButtonGreetCustomer());
})->add(new VerifyShopSignature());

$app->run();
