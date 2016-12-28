<?php

namespace Phpro\SoapClient\Soap\Handler;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7Converter;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

/**
 * Class GuzzleHandle
 *
 * @package Phpro\SoapClient\Soap\Handler
 */
class GuzzleHandle implements MiddlewareSupportingHandlerInterface
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
     * GuzzleHandle constructor.
     *
     * @param ClientInterface $client
     * @param Psr7Converter   $converter
     */
    public function __construct(ClientInterface $client, Psr7Converter $converter)
    {
        $this->client = $client;
        $this->converter = $converter;
    }

    /**
     * @return GuzzleHandle
     */
    public static function createWithDefaultClient(): GuzzleHandle
    {
        return self::createForClient(new Client());
    }

    /**
     * @param ClientInterface $client
     *
     * @return GuzzleHandle
     */
    public static function createForClient(ClientInterface $client): GuzzleHandle
    {
        return new self(
            $client,
            new Psr7Converter(new RequestFactory(), new StreamFactory())
        );
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    public function addMiddleware(MiddlewareInterface $middleware)
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

        $guzzleHandler->push($middleware);
    }

    /**
     * @param SoapRequest $request
     *
     * @return SoapResponse
     */
    public function request(SoapRequest $request): SoapResponse
    {
        // TODO: Is a GuzzleException ok? Willl It convert to a SOAP exception?
        $psr7Request = $this->converter->convertSoapRequest($request);
        $psr7Response = $this->client->send($psr7Request);

        return $this->converter->convertSoapResponse($psr7Response);
    }
}
