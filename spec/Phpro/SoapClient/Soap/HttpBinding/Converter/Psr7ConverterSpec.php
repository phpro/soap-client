<?php

namespace spec\Phpro\SoapClient\Soap\HttpBinding\Converter;

use Interop\Http\Factory\RequestFactoryInterface;
use Interop\Http\Factory\StreamFactoryInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7Converter;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * Class Psr7ConverterSpec
 */
class Psr7ConverterSpec extends ObjectBehavior
{
    function let(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->beConstructedWith($requestFactory, $streamFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Psr7Converter::class);
    }

    function it_can_create_a_request(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $requestFactory->createRequest('POST', '/url')->willReturn($request = new Request());
        $streamFactory->createStream('request')->willReturn($stream = new Stream('php://memory', 'r+'));
        $soapRequest = new SoapRequest('request', '/url', 'action', 1, 0);

        $result = $this->convertSoapRequest($soapRequest);
        $result->shouldBeAnInstanceOf(Request::class);
        $result->getBody()->shouldBe($stream);
    }

    function it_can_create_a_response()
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write('response');
        $stream->rewind();
        $response = (new Response())->withBody($stream);

        $result = $this->convertSoapResponse($response);
        $result->shouldBeAnInstanceOf(SoapResponse::class);
        $result->getResponse()->shouldBe('response');
    }
}
