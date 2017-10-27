<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phpro\SoapClient;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\ClientInterface;
use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientSpec extends ObjectBehavior
{
    function let(SoapClient $soapClient, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($soapClient, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_should_be_a_client()
    {
        $this->shouldImplement(ClientInterface::class);
    }
}
