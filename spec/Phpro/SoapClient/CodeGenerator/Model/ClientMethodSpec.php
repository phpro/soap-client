<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use PhpSpec\ObjectBehavior;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\XsdType;

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
        $this->beConstructedWith(
            'testMethod',
            [],
            ReturnType::fromMetaData('ParamNamespace', XsdType::create('CreditResponse')),
            'ParamNamespace',
            new MethodMeta()
        );
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

    function it_can_count_parameters(): void
    {
        $this->getParametersCount()->shouldBe(0);
    }

    function is_has_a_return_type()
    {
        $this->getReturnType()->shouldBeLike(
            ReturnType::fromMetaData('ParamNamespace', XsdType::create('CreditResponse'))
        );
    }

    function it_has_a_parameter_namespace()
    {
        $this->getParameterNamespace()->shouldBe('ParamNamespace');
    }

    public function it_has_type_meta(): void
    {
        $this->getMeta()->shouldBeLike(new MethodMeta());
    }
}
