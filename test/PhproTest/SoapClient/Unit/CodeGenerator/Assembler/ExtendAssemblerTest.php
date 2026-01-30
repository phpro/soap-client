<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ExtendAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ExtendAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ExtendAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new ExtendAssembler(\ArrayIterator::class);
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new ExtendAssembler(\ArrayIterator::class);
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_type()
    {
        $assembler = new ExtendAssembler(\ArrayIterator::class);
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use ArrayIterator;

class MyType extends ArrayIterator
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_type_with_extended_class_name_equal_to_generated_class_name()
    {
        $assembler = new ExtendAssembler('MyNamespace\\MyType');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @return TypeContext
     */
    private function createContext()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('int'))),
        ], XsdType::create('MyType'));

        return new TypeContext($class, $type);
    }
}
