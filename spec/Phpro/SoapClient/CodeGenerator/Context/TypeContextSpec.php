<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhpSpec\ObjectBehavior;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class TypeContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin TypeContext
 */
class TypeContextSpec extends ObjectBehavior
{
    private Type $type;

    function let(ClassGenerator $class)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());
        $this->type = new Type(
            $context,
            'MyType',
            'MyType',
            [],
            XsdType::create('MyType')
        );
        $this->beConstructedWith($class, $this->type, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_class_generator(ClassGenerator $class)
    {
        $this->getClass()->shouldReturn($class);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn($this->type);
    }
}
