<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhpSpec\ObjectBehavior;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class PropertyContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin PropertyContext
 */
class PropertyContextSpec extends ObjectBehavior
{
    private Type $type;
    private Property $property;

    function let(ClassGenerator $class)
    {
        $destination = new Destination('src/type', 'MyNamespace');
        $namespaceMap = TypeNamespaceMap::create($destination);
        $context = new CodeGeneratorContext($namespaceMap, new DefaultCodingStandardsStrategy());

        $this->property = new Property('prop1', 'string', $context, 'MyNamespace', XsdType::create('string'));
        $this->type = new Type(
            $context,
            'MyType',
            'MyType',
            [$this->property],
            XsdType::create('MyType')
        );

        $this->beConstructedWith($class, $this->type, $this->property, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyContext::class);
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

    function it_has_a_property()
    {
        $this->getProperty()->shouldReturn($this->property);
    }
}
