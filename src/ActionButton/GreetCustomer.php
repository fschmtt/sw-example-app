<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\ActionButton;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GreetCustomer
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /*$response->getBody()->write(json_encode([
            'actionType' => 'notification',
            'payload' => [
                'status' => 'success',
                'message' => 'Customer has been greeted!'
            ]
        ], JSON_THROW_ON_ERROR));*/

        $response->getBody()->write(json_encode([
            'actionType' => 'openModal',
            'payload' => [
                'iframeUrl' => 'http://localhost:8181/modules/greetings',
                'size' => 'large',
                'expand' => true
            ]
        ], JSON_THROW_ON_ERROR));

        $response->getBody()->rewind();

        $signature = hash_hmac('sha256', $response->getBody()->getContents(), $request->getAttribute('SHOP_SECRET'));

        $response = $response->withHeader(
            'shopware-app-signature',
            $signature
        );

        $response->getBody()->rewind();

        return $response->withHeader('Content-Type', 'application/json');
    }
}
