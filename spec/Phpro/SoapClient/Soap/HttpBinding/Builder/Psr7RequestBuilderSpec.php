<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Soap\HttpBinding\Builder;

use Interop\Http\Factory\RequestFactoryInterface;
use Interop\Http\Factory\StreamFactoryInterface;
use Phpro\SoapClient\Exception\RequestException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\Builder\Psr7RequestBuilder;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

/**
 * Class Psr7RequestBuilderSpec
 */
class Psr7RequestBuilderSpec extends ObjectBehavior
{
    function let(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->beConstructedWith($requestFactory, $streamFactory);
        $requestFactory->createRequest(Argument::cetera())->will(function ($arguments) {
            return new Request($arguments[1], $arguments[0]);
        });

        $streamFactory->createStream(Argument::type('string'))->will(function ($arguments) {
            $stream = new Stream('php://memory', 'r+');
            $stream->write($arguments[0]);
            $stream->rewind();

            return $stream;
        });
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Psr7RequestBuilder::class);
    }

    function it_can_create_soap11_requests()
    {
        $this->isSOAP11();
        $this->setHttpMethod('POST');
        $this->setEndpoint($endpoint = 'http://www.endpoint.com');
        $this->setSoapAction($action = 'http://www.soapaction.com');
        $this->setSoapMessage($content = 'content');

        $result = $this->getHttpRequest();
        $result->shouldBeAnInstanceOf(RequestInterface::class);
        $result->getMethod()->shouldBe('POST');
        $result->getHeader('Content-Type')[0]->shouldBe('text/xml; charset="utf-8"');
        $result->getHeader('Content-Length')[0]->shouldBe((string) strlen($content));
        $result->hasHeader('SOAPAction')->shouldBe(true);
        $result->getHeader('SOAPAction')[0]->shouldBe($action);
        $result->getUri()->__toString()->shouldBe($endpoint);
    }

    function it_can_not_use_GET_method_with_soap11()
    {
        $this->isSOAP11();
        $this->setHttpMethod('GET');
        $this->setEndpoint($endpoint = 'http://www.endpoint.com');
        $this->setSoapAction($action = 'http://www.soapaction.com');
        $this->setSoapMessage($content = 'content');

        $this->shouldThrow(RequestException::class)->duringGetHttpRequest();
    }

    function it_can_create_soap12_requests()
    {
        $this->isSOAP12();
        $this->setHttpMethod('POST');
        $this->setEndpoint($endpoint = 'http://www.endpoint.com');
        $this->setSoapAction($action = 'http://www.soapaction.com');
        $this->setSoapMessage($content = 'content');

        $result = $this->getHttpRequest();
        $result->shouldBeAnInstanceOf(RequestInterface::class);
        $result->getMethod()->shouldBe('POST');
        $result->getHeader('Content-Type')[0]->shouldBe('application/soap+xml; charset="utf-8"; action="http://www.soapaction.com"');
        $result->getHeader('Content-Length')[0]->shouldBe((string) strlen($content));
        $result->hasHeader('SOAPAction')->shouldBe(false);
        $result->getUri()->__toString()->shouldBe($endpoint);
    }

    function it_can_use_GET_method_with_soap12()
    {
        $this->isSOAP12();
        $this->setHttpMethod('GET');
        $this->setEndpoint($endpoint = 'http://www.endpoint.com');
        $this->setSoapAction($action = 'http://www.soapaction.com');
        $this->setSoapMessage($content = 'content');

        $result = $this->getHttpRequest();
        $result->shouldBeAnInstanceOf(RequestInterface::class);
        $result->getMethod()->shouldBe('GET');
        $result->hasHeader('Content-Type')->shouldBe(false);
        $result->hasHeader('Content-Length')->shouldBe(false);
        $result->hasHeader('SOAPAction')->shouldBe(false);
        $result->getHeader('Accept')[0]->shouldBe('application/soap+xml');
        $result->getUri()->__toString()->shouldBe($endpoint);
        $result->getBody()->getContents()->shouldBe('');
    }

    function it_can_not_use_PUT_method_with_soap12()
    {
        $this->isSOAP12();
        $this->setHttpMethod('PUT');
        $this->setEndpoint($endpoint = 'http://www.endpoint.com');
        $this->setSoapAction($action = 'http://www.soapaction.com');
        $this->setSoapMessage($content = 'content');

        $this->shouldThrow(RequestException::class)->duringGetHttpRequest();
    }

    function it_needs_an_endpoint()
    {
        $this->setSoapMessage('content');
        $this->shouldThrow(RequestException::class)->duringGetHttpRequest();
    }

    function it_needs_a_message()
    {
        $this->setEndpoint('http://www.endpoint.com');
        $this->shouldThrow(RequestException::class)->duringGetHttpRequest();
    }
}
