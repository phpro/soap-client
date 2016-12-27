<?php

namespace Phpro\SoapClient\Soap\Handler;

use Phpro\SoapClient\Middleware\MiddlewareInterface;

/**
 * Interface MiddlewareSupportingHandler
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
interface MiddlewareSupportingHandler
{
    /**
     * @param MiddlewareInterface $middleware
     *
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware);
}
