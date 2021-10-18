<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Greet
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        # TODO Send out a nice greeting to the customer

        return $response->withHeader('Content-Type', 'application/json');
    }
}
