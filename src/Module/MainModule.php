<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Module;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainModule
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write(file_get_contents(__DIR__ . '/../../public/module.html'));

        return $response;
    }
}
