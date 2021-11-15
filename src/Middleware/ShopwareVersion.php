<?php

declare(strict_types=1);

namespace Fschmtt\SwExampleApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ShopwareVersion
{
    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $shopwareVersion = $request->getHeaderLine('sw-version') === '' ? null : $request->getHeaderLine('sw-version');

        $request = $request->withAttribute('SHOP_SHOPWARE_VERSION', $shopwareVersion);

        return $requestHandler->handle($request);
    }
}
