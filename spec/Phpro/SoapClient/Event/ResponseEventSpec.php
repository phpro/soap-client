<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Event\ResponseEvent;
use Phpro\SoapClient\Type\ResultInterface;
use PhpSpec\ObjectBehavior;

class ResponseEventSpec extends ObjectBehavior
{
    function let(RequestEvent $requestEvent, ResultInterface $response)
    {
        $this->beConstructedWith($requestEvent, $response);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResponseEvent::class);
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
