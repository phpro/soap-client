<?php

namespace Phpro\SoapClient\Middleware;

use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Middleware
 *
 * @package Phpro\SoapClient\Middleware
 */
class Middleware implements MiddlewareInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'empty_middleware';
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $this->beforeRequest($next, $request)
            ->then(
                (function (ResponseInterface $response) {
                    return $this->afterResponse($response);
                })->bindTo($this),
                (function (Exception $exception) {
                    $this->onError($exception);
                })->bindTo($this)
            );
    }

    public function beforeRequest(callable $next, RequestInterface $request): Promise
    {
        return $next($request);
    }

    public function afterResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    public function onError(Exception $exception): void
    {
        throw $exception;
    }
}
