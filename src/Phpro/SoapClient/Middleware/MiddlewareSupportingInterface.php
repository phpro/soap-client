<?php

namespace Phpro\SoapClient\Middleware;

/**
 * Class MiddlewareSupportingInterface
 *
 * @package Phpro\SoapClient\Middleware
 */
interface MiddlewareSupportingInterface
{
    /**
     * @param MiddlewareInterface $middleware
     *
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware);
}
