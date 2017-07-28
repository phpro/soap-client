<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use PhpSpec\ObjectBehavior;

/**
 * Class ClientSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Property
 */
class ClientSpec extends ObjectBehavior
{
    function let(ClientMethodMap $methods)
    {
        $this->beConstructedWith('MyClient', 'MyNamespace', $methods);
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
