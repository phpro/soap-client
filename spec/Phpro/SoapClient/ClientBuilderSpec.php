<?php

namespace spec\Phpro\SoapClient;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\ClientBuilder;
use Phpro\SoapClient\ClientFactory;
use PhpSpec\ObjectBehavior;

class ClientBuilderSpec extends ObjectBehavior
{
    function let()
    {
        $wsdl = realpath(__DIR__ . '/../../../test/fixtures/wsdl/wheater-ws.wsdl');
        $this->beConstructedWith(new ClientFactory(Client::class), $wsdl, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientBuilder::class);
    }

    function it_should_load_a_new_client()
    {
        $this->build()->shouldBeAnInstanceOf(Client::class);
    }
}