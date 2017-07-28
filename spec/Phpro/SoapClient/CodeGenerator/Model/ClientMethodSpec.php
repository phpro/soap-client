<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use PhpSpec\ObjectBehavior;

/**
 * Class MethodSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin Property
 */
class MethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('CreditResponse Credit(Credit $parameters)', 'ParamNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientMethod::class);
    }

    function it_has_a_methodname()
    {
        $this->getMethodName()->shouldReturn('credit');
    }

    function it_has_parameters()
    {
        $this->getParameters()->shouldBeArray();
    }

    function is_has_a_return_type()
    {
        $this->getReturnType()->shouldBe('CreditResponse');
    }
    function it_has_a_parameter_namespace()
    {
        $this->getParameterNamespace()->shouldBe('ParamNamespace');
    }
}
