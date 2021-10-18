<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Register
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /*$headers = '';
        foreach ($request->getHeaders() as $name => $values) {
            $headers .= $name . ': ' . implode(', ', $values);
        }

        file_put_contents(
            __DIR__ . '/registration.txt',
            $request->getUri()->getQuery() ."\n". $headers ."\n". $request->getParsedBody()
        );*/

        $appSecret = 'sw-example-app-1337';
        $appName = 'SwExampleApp';

        $queryString = $request->getUri()->getQuery();

        parse_str($queryString, $queryValues);
        $proof = \hash_hmac(
            'sha256',
            $queryValues['shop-id'] . $queryValues['shop-url'] . $appName,
            $appSecret
        );

        $shopSecret = bin2hex(random_bytes(32));

        file_put_contents(
            sprintf(__DIR__ . '/../../shops/%s.json', $queryValues['shop-id']),
            json_encode(['shopSecret' => $shopSecret], JSON_THROW_ON_ERROR)
        );

        $response->getBody()->write(
            json_encode([
                'proof' => $proof,
                'secret' => $shopSecret,
                'confirmation_url' => sprintf('%s/registration/confirm', $_SERVER['BACKEND_URL']),
            ])
        );

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
