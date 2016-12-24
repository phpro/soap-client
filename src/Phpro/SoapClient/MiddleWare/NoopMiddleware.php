<?php

namespace Phpro\SoapClient\MiddleWare;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SomeMiddleware
 *
 * @package Phpro\SoapClient\MiddleWare
 *
 * TODO: Implement ClientMiddlewareInterface ... ?
 */
class NoopMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $requestBody = $request->getBody()->getContents();
        // DO SOME XML MANIPULATIONS and store in the body

        $response =  $delegate->process($request);
        $responseBody = $response->getBody()->getContents();
        // DO some XMl manipulations and store in the response

        return $response;
    }
}
