<?php

namespace spec\Phpro\SoapClient;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
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

    function it_should_load_a_new_client(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
        $this->factory($engine, $dispatcher)->shouldReturnAnInstanceOf(Client::class);
    }
}
