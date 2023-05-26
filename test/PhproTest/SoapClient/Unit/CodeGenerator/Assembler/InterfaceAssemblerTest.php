<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\InterfaceAssembler;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class InterfaceAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class InterfaceAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new InterfaceAssembler(\Iterator::class);
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new InterfaceAssembler(\Iterator::class);
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new InterfaceAssembler('MyUsedClass');
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [], new TypeMeta());
        $property = Property::fromMetaData('ns1', new MetaProperty('prop1', XsdType::guess('string')));
        $context = new PropertyContext($class, $type, $property);
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new InterfaceAssembler(\Iterator::class);
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Iterator;

class MyType implements Iterator
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
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            Property::fromMetaData($namespace, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespace, new MetaProperty('prop2', XsdType::guess('int'))),
        ], new TypeMeta());

        return new TypeContext($class, $type);
    }
}
