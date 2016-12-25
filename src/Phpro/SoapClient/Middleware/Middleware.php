<?php

namespace Phpro\SoapClient\MiddleWare;

use GuzzleHttp\Promise\PromiseInterface;
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
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return (function (RequestInterface $request, array $options) use ($handler) {
            return $this->beforeRequest($handler, $request, $options)
                ->then(
                    (function (ResponseInterface $response) {
                        return $this->afterResponse($response);
                    })->bindTo($this)
                );
        })->bindTo($this);
    }

    /**
     * @param callable         $handler
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        return $handler($request, $options);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response)
    {
        return $response;
    }
}
