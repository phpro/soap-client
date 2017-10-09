<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpro\SoapClient\Wsdl\Provider;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use InvalidArgumentException;
use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Middleware\MiddlewareSupportingInterface;
use Phpro\SoapClient\Util\Filesystem;

/**
 * Class LocalWsdlProvider
 *
 * @package Phpro\SoapClient\Wsdl\Provider
 */
class GuzzleWsdlProvider implements WsdlProviderInterface, MiddlewareSupportingInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var string
     */
    private $location = '';

    /**
     * GuzzleWsdlProvider constructor.
     *
     * @param ClientInterface $client
     * @param Filesystem      $filesystem
     */
    public function __construct(ClientInterface $client, Filesystem $filesystem)
    {
        $this->client = $client;
        $this->filesystem = $filesystem;
    }

    /**
     * @param ClientInterface $client
     *
     * @return GuzzleWsdlProvider
     */
    public static function createForClient(ClientInterface $client)
    {
        return new self($client, new Filesystem());
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    /**
     * @param string $source
     *
     * @return string
     * @throws \Phpro\SoapClient\Exception\WsdlException
     */
    public function provide(string $source): string
    {
        $this->registerMiddlewares();
        $location = $this->getLocation() ?: tempnam(sys_get_temp_dir(), 'phpro-soap-client-wsdl');

        if (!$this->filesystem->fileExists($location)) {
            throw WsdlException::notFound($source);
        }

        try {
            $response = $this->client->request('GET', $source);
            $this->filesystem->putFileContents($location, (string) $response->getBody());
        } catch (\Exception $exception) {
            throw WsdlException::fromException($exception);
        }

        $this->unregisterMiddlewares();

        return $location;
    }

    /**
     * @return HandlerStack
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    private function fetchHandlerStack(): HandlerStack
    {
        $guzzleHandler = $this->client->getConfig('handler');
        if (!$guzzleHandler instanceof HandlerStack) {
            throw new InvalidArgumentException(
                sprintf(
                    'Current guzzle client handler "%s" does not support middlewares. Use the HandlerStack instead.',
                    get_class($guzzleHandler)
                )
            );
        }

        return $guzzleHandler;
    }

    /**
     * Register the middlewares before fetching the results.
     */
    private function registerMiddlewares()
    {
        $handlerStack = $this->fetchHandlerStack();
        foreach ($this->middlewares as $middleware) {
            $handlerStack->push($middleware, $middleware->getName());
        }
    }

    /**
     * Unregister the middlewares so that they are only used for fetching the WSDLs
     */
    private function unregisterMiddlewares()
    {
        $handlerStack = $this->fetchHandlerStack();
        foreach ($this->middlewares as $middleware) {
            $handlerStack->remove($middleware);
        }
    }
}
