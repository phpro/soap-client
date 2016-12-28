<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Middleware\MiddlewareInterface;

/**
 * Interface MiddlewareSupportingHandlerInterface
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
interface MiddlewareSupportingHandlerInterface extends HandlerInterface
{
    /**
     * @param MiddlewareInterface $middleware
     *
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware);
}
