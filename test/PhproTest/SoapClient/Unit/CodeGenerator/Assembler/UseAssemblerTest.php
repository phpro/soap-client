<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\UseAssembler;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
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
 * Class UseAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class UseAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_can_assemble_property_context()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [], XsdType::create('MyType'));
        $ns1Namespaces = $this->createTypeNamespaceMap('ns1');
        $property = Property::fromMetaData($ns1Namespaces, new MetaProperty('prop1', XsdType::guess('string')));
        $context = new PropertyContext($class, $type, $property);
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_type()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass;

class MyType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_type_with_alias()
    {
        $assembler = new UseAssembler('MyUsedClass', 'Alias');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass as Alias;

class MyType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_an_existing_use_with_alias()
    {
        $assembler = new UseAssembler('MyUsedClass', 'Alias');
        $context = $this->createContext();
        $context->getClass()->addUse('MyUsedClass');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass;

class MyType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_does_not_assemble_use_for_the_same_namespace()
    {
        $assembler = new UseAssembler('MyNamespace');
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

    #[Test]
    function it_does_not_assemble_use_for_the_same_namespace_but_different_class()
    {
        $assembler = new UseAssembler('MyNamespace\\SomeOtherClass');
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

    #[Test]
    function it_does_not_assemble_use_for_the_global_namespace()
    {
        $assembler = new UseAssembler('SomeOtherClass');
        $class = new ClassGenerator('MyType');
        $namespaces = $this->createTypeNamespaceMap('');
        $type = new Type($namespaces, 'MyType', 'MyType', [], XsdType::create('MyType'));
        $context = new TypeContext($class, $type);

        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
class MyType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }


    #[Test]
    function it_assembles_use_for_the_different_namespace()
    {
        $assembler = new UseAssembler('DifferentNamespace\\SomeOtherClass');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use DifferentNamespace\SomeOtherClass;

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
        $type = new Type($namespaces, 'MyType', 'MyType', [], XsdType::create('MyType'));

        return new TypeContext($class, $type);
    }
}
