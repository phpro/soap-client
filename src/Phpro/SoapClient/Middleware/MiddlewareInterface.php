<?php

namespace Phpro\SoapClient\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface MiddlewareInterface
 *
 * @package Phpro\SoapClient\Middleware
 */
interface MiddlewareInterface
{
    /**
     * Execute this middleware.
     *
     * @param  ServerRequestInterface $request  The PSR7 request.
     * @param  ResponseInterface      $response The PSR7 response.
     * @param  callable               $next     The Next middleware.
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable  $next);
}
