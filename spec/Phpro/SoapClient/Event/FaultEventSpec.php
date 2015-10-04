<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Event\RequestEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

class FaultEventSpec extends ObjectBehavior
{
    function let(Client $client,\SoapFault $soapFault, RequestEvent $requestEvent)
    {
        $this->beConstructedWith($client, $soapFault, $requestEvent);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FaultEvent::class);
    }

    function it_is_an_event()
    {
        $this->shouldHaveType(Event::class);
    }

    function it_should_know_the_request_event(RequestEvent $requestEvent)
    {
        $this->getRequestEvent()->shouldReturn($requestEvent);
    }

    function it_should_know_the_fault(\SoapFault $soapFault)
    {
        $this->getSoapFault()->shouldReturn($soapFault);
    }

    function it_should_know_the_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
