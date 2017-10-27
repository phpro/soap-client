<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
