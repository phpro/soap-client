<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use PhpSpec\ObjectBehavior;

/**
 * Class ClientMethodSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Model
 * @mixin ClientMethod
 */
class ClientMethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('testMethod', [], 'CreditResponse', 'ParamNamespace');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientMethod::class);
    }

    function it_has_a_methodname()
    {
        $this->getMethodName()->shouldReturn('testMethod');
    }

    function it_has_parameters()
    {
        $this->getParameters()->shouldBeArray();
    }

    function is_has_a_return_type()
    {
        $this->getReturnType()->shouldBe('CreditResponse');
    }

    function it_transforms_return_type()
    {
        $this->beConstructedWith('testMethod', [], 'credit_response', 'ParamNamespace');
        $this->getReturnType()->shouldBe('CreditResponse');
    }

    function it_has_a_parameter_namespace()
    {
        $this->getParameterNamespace()->shouldBe('ParamNamespace');
    }

    function it_has_namespaced_return_type()
    {
        $this->getNamespacedReturnType()->shouldBe('\\ParamNamespace\\CreditResponse');
    }
}
