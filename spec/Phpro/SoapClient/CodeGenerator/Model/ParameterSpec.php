<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use PhpSpec\ObjectBehavior;

/**
 * Class ParameterSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Property
 */
class ParameterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('MyParameter', 'MyParameterType');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parameter::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('MyParameter');
    }

    function is_has_a_namespace()
    {
        $this->getNamespace()->shouldBe('MyParameterType');
    }
}
