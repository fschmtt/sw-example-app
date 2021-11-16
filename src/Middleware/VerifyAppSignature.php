<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class VerifyAppSignature
{
    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $errorResponse = new \Slim\Psr7\Response(400);
        $errorResponse = $errorResponse->withHeader('Content-Type', 'application/json');

        if (!$appSignature = $request->getHeaderLine('shopware-app-signature')) {
                $errorResponse->getBody()->write(json_encode([
                    'error' => 'ERR_MISSING_SHOPWARE_APP_SIGNATURE_HEADER',
                    'description' => 'The request is missing the "shopware-app-signature" header'
                ], JSON_THROW_ON_ERROR));

            return $errorResponse;
        }

        $queryString = $request->getUri()->getQuery();
        $hmac = hash_hmac('sha256', $queryString, $_SERVER['APP_SECRET']);

        if (!hash_equals($appSignature, $hmac)) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_INVALID_APP_SIGNATURE',
                'description' => 'The "shopware-app-signature" is invalid'
            ], JSON_THROW_ON_ERROR));

            return $errorResponse->withStatus(401);
        }

        return $requestHandler->handle($request);
    }
}
