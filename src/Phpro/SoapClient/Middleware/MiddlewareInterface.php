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

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MiddlewareInterface
 *
 * @package Phpro\SoapClient\Middleware
 */
interface MiddlewareInterface
{
    /**
     * The invoke method is responsible for calling the beforeRequest and afterRequest method of the middleware.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler);

    /**
     * @param callable         $handler
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options);

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response);

    /**
     * @return string
     */
    public function getName(): string;
}
