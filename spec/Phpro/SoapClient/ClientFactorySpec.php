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
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientFactorySpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith(Client::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientFactory::class);
    }

    function it_should_load_a_new_client(SoapClient $soapClient, EventDispatcherInterface $dispatcher)
    {
        $this->factory($soapClient, $dispatcher)->shouldReturnAnInstanceOf(Client::class);
    }
}
