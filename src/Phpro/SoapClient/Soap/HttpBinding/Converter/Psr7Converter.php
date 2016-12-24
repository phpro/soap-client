<?php

namespace Phpro\SoapClient\Soap\HttpBinding\Converter;

use Interop\Http\Factory\RequestFactoryInterface;
use Interop\Http\Factory\StreamFactoryInterface;
use Phpro\SoapClient\Soap\HttpBinding\Builder\Psr7RequestBuilder;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\Serializer;

/**
 * Class Psr7Converter
 *
 * @package Phpro\SoapClient\Soap\HttpBinding\Converter
 */
class Psr7Converter
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Psr7Converter constructor.
     *
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface  $streamFactory
     */
    public function __construct(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param SoapRequest $request
     *
     * @throws \Phpro\SoapClient\Exception\RequestException
     * @return RequestInterface
     */
    public function convertSoapRequest(SoapRequest $request): RequestInterface
    {
        $builder = new Psr7RequestBuilder($this->requestFactory, $this->streamFactory);

        $request->getVersion() === 1 ? $builder->isSOAP11() : $builder->isSOAP12();
        $builder->setEndpoint($request->getLocation());
        $builder->setSoapAction($request->getAction());
        $builder->setSoapMessage($request->getRequest());

        return $builder->getHttpRequest();
    }

    /**
     * @param ResponseInterface $response
     *
     * @return SoapResponse
     */
    public function convertSoapResponse(ResponseInterface $response): SoapResponse
    {
        return new SoapResponse(
            Serializer::toString($response)
        );
    }
}
