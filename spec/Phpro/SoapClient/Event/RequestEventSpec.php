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
use Phpro\SoapClient\Type\RequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;

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

    function it_should_know_the_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
