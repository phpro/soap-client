<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\SoapEvent;
use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;

class RequestEventSpec extends ObjectBehavior
{
    function let(Client $client, RequestInterface $request)
    {
        $this->beConstructedWith($client, 'method', $request);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\SoapClient\Event\RequestEvent');
    }

    function it_is_an_event()
    {
        $this->shouldHaveType(SoapEvent::class);
    }

    function it_should_know_the_request_method()
    {
        $this->getMethod()->shouldReturn('method');
    }

    function it_should_know_the_request(RequestInterface $request)
    {
        $this->getRequest()->shouldReturn($request);
    }

    function it_should_know_the_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
