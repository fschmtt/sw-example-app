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

        if (!$shopSignature = $request->getHeader('shopware-shop-signature')) {
                $errorResponse->getBody()->write(json_encode([
                    'error' => 'ERR_MISSING_SHOPWARE_SHOP_SIGNATURE_HEADER',
                    'description' => 'The request is missing the "shopware-shop-signature" header'
                ], JSON_THROW_ON_ERROR));

            return $errorResponse;
        }

        $request->withAttribute('SHOP_SIGNATURE', $shopSignature);

        $shopSecret = json_decode(file_get_contents(__DIR__ . '/../../shops/'.$request->getAttribute('SHOP_ID').'.json'), true)['shopSecret'];

        $hmac = \hash_hmac('sha256', $request->getBody()->getContents(), $shopSecret);

        // TODO - Necessary?
        /*if ($shopUrl != $shop['shopUrl']) {
            $errorResponse->getBody()->write(json_encode([
                'error' => 'ERR_SHOP_URL_MISMATCH',
                'description' => 'The provided shop-url does not match the URL used during registration',
            ]), JSON_THROW_ON_ERROR);

            return $errorResponse->withStatus(401);
        }*/

        return $requestHandler->handle($request);
    }
}
