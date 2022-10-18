<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Registration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Register
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $queryString = $request->getUri()->getQuery();

        parse_str($queryString, $queryValues);
        $proof = \hash_hmac(
            'sha256',
            $queryValues['shop-id'] . $queryValues['shop-url'] . $_SERVER['APP_NAME'],
            $_SERVER['APP_SECRET']
        );

        if (!$this->tryShopUrl($queryValues['shop-url'])) {
            $response->getBody()->write(json_encode([
                'error' => 'The provided shop-url is not valid. Please make sure to properly configure your APP_URL.',
            ], JSON_THROW_ON_ERROR));

            return $response->withStatus(400);
        }

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

    private function tryShopUrl(string $shopUrl): bool
    {
        try {
            $httpClient = new Client();
            $httpClient->get($shopUrl);

            return true;
        } catch (ConnectException $e) {
            return false;
        }
    }
}
