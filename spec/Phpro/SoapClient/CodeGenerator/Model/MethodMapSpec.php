<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\MethodMap;
use PhpSpec\ObjectBehavior;

/**
 * Class MethodMapSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 */
class MethodMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([], 'MyNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MethodMap::class);
    }

    function it_has_methods()
    {
        $this->getMethods()->shouldReturn([]);
    }
}
