<?php

namespace spec\Phpro\SoapClient\Soap\Handler;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Middleware\CollectLastRequestInfoMiddleware;
use Phpro\SoapClient\Middleware\MiddlewareSupportingInterface;
use Phpro\SoapClient\Middleware\MiddlewareInterface;
use Phpro\SoapClient\Soap\Handler\HandlerInterface;
use Phpro\SoapClient\Soap\Handler\LastRequestInfoCollectorInterface;
use Phpro\SoapClient\Soap\HttpBinding\Converter\Psr7Converter;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Soap\Handler\GuzzleHandle;

/**
 * Class GuzzleHandleSpec
 */
class GuzzleHandleSpec extends ObjectBehavior
{
    function let(ClientInterface $client, Psr7Converter $converter, CollectLastRequestInfoMiddleware $lastRequestInfoMiddleware)
    {
        $this->beConstructedWith($client, $converter, $lastRequestInfoMiddleware);
        $lastRequestInfoMiddleware->getName()->willReturn('lastRequestInfoMiddleware');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GuzzleHandle::class);
    }

    function it_is_a_soap_handler()
    {
        $this->shouldImplement(HandlerInterface::class);
    }

    function it_is_a_middleware_supporting_soap_handler()
    {
        $this->shouldImplement(MiddlewareSupportingInterface::class);
    }

    function it_is_a_last_request_info_collector()
    {
        $this->shouldImplement(LastRequestInfoCollectorInterface::class);
    }

    function it_can_push_middlewares_to_a_handler_stack(ClientInterface $client, HandlerStack $stackHandler, MiddlewareInterface $middleware)
    {
        $client->getConfig('handler')->willReturn($stackHandler);
        $middleware->getName()->willReturn($middlewareName = 'middleware');
        $stackHandler->push($middleware, $middlewareName)->shouldBeCalled();

        $this->addMiddleware($middleware);
    }

    function it_can_not_push_middlewares_to_an_unsupported_stack(ClientInterface $client, MockHandler $stackHandler, MiddlewareInterface $middleware)
    {
        $client->getConfig('handler')->willReturn($stackHandler);

        $this->shouldThrow(InvalidArgumentException::class)->duringAddMiddleware($middleware);
    }

    function it_can_handle_soap_requests(
        ClientInterface $client,
        Psr7Converter $converter,
        HandlerStack $stackHandler,
        CollectLastRequestInfoMiddleware $lastRequestInfoMiddleware
    ) {
        $soapRequest = new SoapRequest('request', 'location', 'action', 1, 0);
        $soapResponse = new SoapResponse('response');
        $request = new Request('POST', 'location');
        $response = new Response();

        $converter->convertSoapRequest($soapRequest)->willReturn($request);
        $converter->convertSoapResponse($response)->willReturn($soapResponse);
        $client->getConfig('handler')->willReturn($stackHandler);
        $client->send($request)->willReturn($response);

        $stackHandler->remove($lastRequestInfoMiddleware)->shouldBeCalled();
        $stackHandler->push($lastRequestInfoMiddleware)->shouldBeCalled();

        $this->request($soapRequest)->shouldBe($soapResponse);
    }
}
