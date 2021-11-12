<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Webhook;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GreetCustomer
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        file_put_contents(__DIR__ . '/greet.json', json_encode($data));

        /*$response->getBody()->write(json_encode([
            'actionType' => 'notification',
            'payload' => [
                'status' => 'success',
                'message' => 'Customer has been greeted!'
            ]
        ], JSON_THROW_ON_ERROR));*/

        $shopSecret = '4b5bcb4fca82852b1fb86bae33e1c06a950184964c08b41751d93a8669e596d';
        $query = 'shop-id=a55DiNqvA08OISQn';
        $signature = hash_hmac('sha256', $query, $shopSecret);

        $response->getBody()->write(json_encode([
            'actionType' => 'openModal',
            'payload' => [
                'iframeUrl' => sprintf('http://localhost:8181/greetings/module?%s&shopware-shop-signature=%s', $query, $signature),
                'size' => 'large',
                'expand' => true
            ]
        ], JSON_THROW_ON_ERROR));

        $response->getBody()->rewind();

        $response = $response->withHeader(
            'shopware-app-signature',
            hash_hmac('sha256', $response->getBody()->getContents(), $shopSecret)
        );

        $response->getBody()->rewind();

        return $response->withHeader('Content-Type', 'application/json');
    }
}
