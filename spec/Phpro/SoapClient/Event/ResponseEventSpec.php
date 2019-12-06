<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\SoapEvent;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Type\ResultInterface;
use PhpSpec\ObjectBehavior;

class ResponseEventSpec extends ObjectBehavior
{
    function let(Client $client, RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->beConstructedWith($client, $requestEvent, $response);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResponseEvent::class);
    }

    function it_is_an_event()
    {
        $this->shouldHaveType(SoapEvent::class);
    }

    function it_should_know_the_request_event(RequestEvent $requestEvent)
    {
        $this->getRequestEvent()->shouldReturn($requestEvent);
    }

    function it_should_know_the_result(ResultInterface $response)
    {
        $this->getResponse()->shouldReturn($response);
    }
}
