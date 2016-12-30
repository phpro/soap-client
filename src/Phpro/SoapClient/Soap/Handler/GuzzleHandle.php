<?php

namespace Phpro\SoapClient\Soap\Handler;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Middleware\CollectLastRequestInfoMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7Converter;
use Phpro\SoapClient\Soap\HttpBinding\LastRequestInfo;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class GuzzleHandle
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
class GuzzleHandle implements MiddlewareSupportingHandlerInterface, LastRequestInfoCollectorInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Psr7Converter
     */
    private $converter;

    /**
     * @var LastRequestInfoCollectorInterface|MiddlewareInterface
     */
    private $lastRequestInfoCollector;

    /**
     * GuzzleHandle constructor.
     *
     * @param ClientInterface                   $client
     * @param Psr7Converter                     $converter
     * @param LastRequestInfoCollectorInterface $lastRequestInfoCollector
     *
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function __construct(
        ClientInterface $client,
        Psr7Converter $converter,
        LastRequestInfoCollectorInterface $lastRequestInfoCollector
    ) {
        $this->client = $client;
        $this->converter = $converter;
        $this->lastRequestInfoCollector = $lastRequestInfoCollector;

        if (!$lastRequestInfoCollector instanceof MiddlewareInterface) {
            throw new InvalidArgumentException('The lastRequestInforCollector should also be a middleware!');
        }
    }

    /**
     * @return GuzzleHandle
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public static function createWithDefaultClient(): GuzzleHandle
    {
        return self::createForClient(new Client());
    }

    /**
     * @param ClientInterface $client
     *
     * @return GuzzleHandle
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public static function createForClient(ClientInterface $client): GuzzleHandle
    {
        return new self(
            $client,
            new Psr7Converter(new RequestFactory(), new StreamFactory()),
            new CollectLastRequestInfoMiddleware()
        );
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $stack = $this->fetchHandlerStack();
        $stack->push($middleware, $middleware->getName());
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function removeMiddleware(MiddlewareInterface $middleware)
    {
        $stack = $this->fetchHandlerStack();
        $stack->remove($middleware);
    }

    /**
    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function request(SoapRequest $request): SoapResponse
    {
        $this->pushLastRequestInfoMiddleware();
        $psr7Request = $this->converter->convertSoapRequest($request);
        $psr7Response = $this->client->send($psr7Request);

        return $this->converter->convertSoapResponse($psr7Response);
    }

    /**
     * @return LastRequestInfo
     */
    public function collectLastRequestInfo(): LastRequestInfo
    {
        return $this->lastRequestInfoCollector->collectLastRequestInfo();
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
     * Make sure the lastRequestInfoCollector is added as a middleware
     *
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    private function pushLastRequestInfoMiddleware()
    {
        $handlerStack = $this->fetchHandlerStack();
        $handlerStack->remove($this->lastRequestInfoCollector);
        $handlerStack->push($this->lastRequestInfoCollector);
    }
}
