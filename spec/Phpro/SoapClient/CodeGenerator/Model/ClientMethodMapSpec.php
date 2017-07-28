<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use PhpSpec\ObjectBehavior;

/**
 * Class MethodMapSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 */
class ClientMethodMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([], 'MyNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientMethodMap::class);
    }

    function it_has_methods()
    {
        $this->getMethods()->shouldReturn([]);
    }
}
