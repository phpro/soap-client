<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use PhpSpec\ObjectBehavior;

/**
 * Class ClientSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    function let()
    {
        $destination = new Destination('src/client', 'MyNamespace');
        $clientConfig = new ClientConfig('MyClient', $destination);
        $methods = new ClientMethodMap([]);
        $this->beConstructedWith($clientConfig, $methods);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('MyClient');
    }

    function is_has_a_namespace()
    {
        $this->getNamespace()->shouldBe('MyNamespace');
    }
}
