<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Module;

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainModule
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $client = new Client([
            'base_uri' => 'http:',
            'headers' => [
                'Authorization' => 'Bearer: ' . $request->getAttribute('SHOP_API_KEY'),
            ]
        ]);

        $languages = json_decode($client->get('/api/language'), true, 512, JSON_THROW_ON_ERROR);
        var_dump($languages);
        $response->getBody()->write(file_get_contents(__DIR__ . '/../../public/module.html'));

        return $response;
    }
}
