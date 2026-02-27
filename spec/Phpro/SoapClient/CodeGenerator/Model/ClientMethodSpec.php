<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
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
        $destination = new Destination('src/type', 'ParamNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->beConstructedWith(
            'testMethod',
            [],
            ReturnType::fromMetaData($context, XsdType::create('CreditResponse')),
            $context,
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
        $destination = new Destination('src/type', 'ParamNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->getReturnType()->shouldBeLike(
            ReturnType::fromMetaData($context, XsdType::create('CreditResponse'))
        );
    }

    public function it_has_type_meta(): void
    {
        $this->getMeta()->shouldBeLike(new MethodMeta());
    }
}
