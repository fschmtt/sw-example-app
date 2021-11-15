<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class VerifyShopSignature
{
    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $errorResponse = new \Slim\Psr7\Response(400);
        $errorResponse = $errorResponse->withHeader('Content-Type', 'application/json');

        $shopSignature = $this->getShopwareShopSignatureFromRequest($request);

        if (!$shopSignature) {
                $errorResponse->getBody()->write(json_encode([
                    'error' => 'ERR_MISSING_SHOPWARE_SHOP_SIGNATURE',
                    'description' => 'The request is missing the "shopware-shop-signature"'
                ], JSON_THROW_ON_ERROR));

            return $errorResponse;
        }

        $shopId = $this->getShopIdFromRequest($request);

        if (!$shopId) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_MISSING_SHOP_ID',
                'description' => 'The request is missing the "shopId" in the body and "shop-id" query parameter',
            ]));

            return $errorResponse->withStatus(401);
        }

        $shopSecret = @file_get_contents(
            sprintf(__DIR__ . '/../../shops/%s.json', $shopId)
        );

        if (!$shopSecret) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_SHOP_ID_NOT_REGISTERED',
                'description' => sprintf('The shop-id "%s" is not registered', $shopId),
            ], JSON_THROW_ON_ERROR));

            return $errorResponse->withStatus(401);
        }

        $shop = json_decode($shopSecret, true, JSON_THROW_ON_ERROR);
        $shopSecret = $shop['shopSecret'] ?? '';

        $hmacBody = \hash_hmac('sha256', $request->getBody()->getContents(), $shopSecret);
        $hmacQuery = \hash_hmac('sha256', $this->getQueryStringWithoutShopSignature($request), $shopSecret);

        if (!hash_equals($shopSignature, $hmacBody) && !hash_equals($shopSignature, $hmacQuery)) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_INVALID_SHOP_SIGNATURE',
                'description' => 'The provided "shopware-shop-signature" is invalid'
            ]));

            return $errorResponse->withStatus(401);
        }

        $request = $request->withAttribute('SHOP_ID', $shopId);
        $request = $request->withAttribute('SHOP_SECRET', $shopSecret);
        $request = $request->withAttribute('SHOP_API_KEY', $shop['apiKey']);
        $request = $request->withAttribute('SHOP_SECRET_KEY', $shop['secretKey']);
        $request = $request->withAttribute('SHOP_URL', $shop['shopUrl']);

        return $requestHandler->handle($request);
    }

    private function getShopwareShopSignatureFromRequest(Request $request): ?string
    {
        if ($request->getHeaderLine('shopware-shop-signature') !== '') {
            return $request->getHeaderLine('shopware-shop-signature');
        }

        if (isset($request->getQueryParams()['shopware-shop-signature'])) {
            return $request->getQueryParams()['shopware-shop-signature'];
        }

        return null;
    }

    private function getShopIdFromRequest(Request $request): ?string
    {
        // Registration confirmation request
        if (isset($request->getParsedBody()['shopId'])) {
            return $request->getParsedBody()['shopId'];
        }

        // GET requests (e.g. main module)
        if (isset($request->getQueryParams()['shop-id'])) {
            return $request->getQueryParams()['shop-id'];
        }

        // POST requests (e.g. webhooks, action buttons)
        if (isset($request->getParsedBody()['source']['shopId'])) {
            return $request->getParsedBody()['source']['shopId'];
        }

        return null;
    }

    private function getQueryStringWithoutShopSignature(Request $request): string
    {
        $queryParams = $request->getQueryParams();

        unset($queryParams['shopware-shop-signature']);

        return urldecode(http_build_query($queryParams));
    }
}
