<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding\Builder;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Phpro\SoapClient\Exception\RequestException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\Builder\Psr7RequestBuilder;
use Psr\Http\Message\RequestInterface;

/**
 * Class Psr7RequestBuilderSpec
 */
class Psr7RequestBuilderSpec extends ObjectBehavior
{
    function let(MessageFactory $requestFactory, StreamFactory $streamFactory)
    {
        $this->beConstructedWith($requestFactory, $streamFactory);
        $requestFactory->createRequest(Argument::cetera())->will(function ($arguments) {
            return new Request($arguments[0], $arguments[1]);
        });

        $streamFactory->createStream(Argument::type('string'))->will(function ($arguments) {
            $stream = new Stream(fopen('php://memory', 'rwb'));
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
