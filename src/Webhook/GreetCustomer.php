<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Webhook;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GreetCustomer
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $response->withHeader('Content-Type', 'application/json');
    }
}
