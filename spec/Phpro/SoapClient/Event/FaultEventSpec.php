<?php

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\SoapEvent;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Exception\SoapException;
use PhpSpec\ObjectBehavior;

class FaultEventSpec extends ObjectBehavior
{
    function let(Client $client, SoapException $soapException, RequestEvent $requestEvent)
    {
        $this->beConstructedWith($client, $soapException, $requestEvent);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FaultEvent::class);
    }

    function it_is_an_event()
    {
        $this->shouldHaveType(SoapEvent::class);
    }

    function it_should_know_the_request_event(RequestEvent $requestEvent)
    {
        $this->getRequestEvent()->shouldReturn($requestEvent);
    }

    function it_should_know_the_fault(SoapException $soapException)
    {
        $this->getSoapException()->shouldReturn($soapException);
    }

    function it_should_know_the_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
