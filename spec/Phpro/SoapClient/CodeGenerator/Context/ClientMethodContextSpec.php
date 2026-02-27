<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use PhpSpec\ObjectBehavior;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ClientMethodContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin ClientMethodContext
 */
class ClientMethodContextSpec extends ObjectBehavior
{
    private ClientMethod $method;

    function let(ClassGenerator $class)
    {
        $destination = new Destination('src/type', 'ParamNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());

        $this->method = new ClientMethod(
            'testMethod',
            [],
            ReturnType::fromMetaData($context, XsdType::create('CreditResponse')),
            $context,
            new MethodMeta()
        );

        $this->beConstructedWith($class, $this->method, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientMethodContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_class_generator(ClassGenerator $class)
    {
        $this->getClass()->shouldReturn($class);
    }

    function it_has_a_method()
    {
        $this->getMethod()->shouldReturn($this->method);
    }
}
