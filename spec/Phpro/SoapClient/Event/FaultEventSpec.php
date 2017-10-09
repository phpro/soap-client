<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient\Event;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event\FaultEvent;
use Phpro\SoapClient\Event\RequestEvent;
use Phpro\SoapClient\Exception\SoapException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

class FaultEventSpec extends ObjectBehavior
{
    function let(Client $client,SoapException $soapException, RequestEvent $requestEvent)
    {
        $this->beConstructedWith($client, $soapException, $requestEvent);
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

    function it_should_know_the_fault(SoapException $soapException)
    {
        $this->getSoapException()->shouldReturn($soapException);
    }

    function it_should_know_the_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
