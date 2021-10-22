<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MetaInformation
{
    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $request = $this->detectShopwareVersion($request);

        $errorResponse = new \Slim\Psr7\Response(400);
        $errorResponse = $errorResponse->withHeader('Content-Type', 'application/json');

        parse_str($request->getUri()->getQuery(), $queryParams);

        if (!$shopId = $queryParams['shop-id'] ?? null) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_MISSING_QUERY_PARAMETER_SHOP_ID',
                'description' => 'The request is missing the "shop-id" query parameter'
            ], JSON_THROW_ON_ERROR));

            return $errorResponse;
        }

        $request = $request->withAttribute('SHOP_ID', $shopId);

        if (!$shopUrl = $queryParams['shop-url'] ?? null) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_MISSING_QUERY_PARAMETER_SHOP_URL',
                'description' => 'The request is missing the "shop-url" query parameter'
            ], JSON_THROW_ON_ERROR));

            return $errorResponse;
        }

        $request = $request->withAttribute('SHOP_URL', $shopUrl);

        return $requestHandler->handle($request);
    }
}
