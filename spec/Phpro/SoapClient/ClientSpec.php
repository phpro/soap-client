<?php

namespace spec\Phpro\SoapClient;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\ClientInterface;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Soap\SoapClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientSpec extends ObjectBehavior
{
    function let(EngineInterface $engine, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($engine, $dispatcher);
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
