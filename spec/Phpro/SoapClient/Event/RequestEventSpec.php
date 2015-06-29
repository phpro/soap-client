<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

class RequestEventSpec extends ObjectBehavior
{
    function let(RequestInterface $request)
    {
        $this->beConstructedWith('method', $request);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\SoapClient\Event\RequestEvent');
    }

    function it_is_an_event()
    {
        $this->shouldHaveType(Event::class);
    }

    function it_should_know_the_request_method()
    {
        $this->getMethod()->shouldReturn('method');
    }

    function it_should_know_the_request(RequestInterface $request)
    {
        $this->getRequest()->shouldReturn($request);
    }
}
