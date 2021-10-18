<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Confirm
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /*$headers = '';
        foreach ($request->getHeaders() as $name => $values) {
            $headers .= $name . ': ' . implode(', ', $values);
        }

        file_put_contents(
            __DIR__ . '/confirm.txt',
            $request->getUri()->getQuery() ."\n". $headers ."\n". (string) $request->getBody()
        );*/

        $shop = $request->getParsedBody();

        $shopSecret = @file_get_contents(sprintf(__DIR__ . '/../../shops/%s.json', $shop['shopId']));

        if (!$shopSecret) {
            $response->getBody()->write(json_encode([
                'error' => 'ERR_SHOP_ID_NOT_REGISTERED',
                'description' => sprintf('The shop-id "%s" is not registered', $shop['shopId']),
            ], JSON_THROW_ON_ERROR));

            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $shopSecret = json_decode(
            file_get_contents(sprintf(__DIR__ . '/../../shops/%s.json', $shop['shopId'])),
            true,
            JSON_THROW_ON_ERROR
        );

        $shop = array_merge($shop, $shopSecret);

        file_put_contents(
            sprintf(__DIR__ . '/../../shops/%s.json', $shop['shopId']),
            json_encode($shop, JSON_THROW_ON_ERROR)
        );

        return $response->withStatus(200);
    }
}
