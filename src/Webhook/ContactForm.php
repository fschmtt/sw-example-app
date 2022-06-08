<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Webhook;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ContactForm
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        file_put_contents(__DIR__ . '/contact-form.json', json_encode($body));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
