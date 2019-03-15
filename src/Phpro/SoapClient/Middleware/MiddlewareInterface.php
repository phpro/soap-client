<?php

namespace Phpro\SoapClient\Middleware;

use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MiddlewareInterface
 *
 * @package Phpro\SoapClient\Middleware
 */
interface MiddlewareInterface extends Plugin
{

    public function beforeRequest(callable $handler, RequestInterface $request): Promise;

    public function afterResponse(ResponseInterface $response): ResponseInterface;

    public function onError(Exception $exception);

    public function getName(): string;
}
