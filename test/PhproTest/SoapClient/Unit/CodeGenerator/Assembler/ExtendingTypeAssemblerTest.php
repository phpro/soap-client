<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ExtendingTypeAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\TypeMeta;

/**
 * Class ExtendingTypeAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ExtendingTypeAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ExtendingTypeAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new ExtendingTypeAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new ExtendingTypeAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use \MyNamespace\MyBaseType;

class MyType extends MyBaseType
{
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_skips_assambling_on_non_extending_type()
    {
        $assembler = new ExtendingTypeAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [], new TypeMeta());

        $context = new TypeContext($class, $type);
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
     * @test
     */
    function it_skips_assambling_on_extending_simple_type()
    {
        $assembler = new ExtendingTypeAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [], (new TypeMeta())->withExtends([
            'type' => 'string',
            'namespace' => 'xsd',
            'isSimple' => true,
        ]));

        $context = new TypeContext($class, $type);
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
        $type = new Type('MyNamespace', 'MyType', [], (new TypeMeta())->withExtends([
            'type' => 'MyBaseType',
            'namespace' => 'xxxx'
        ]));

        return new TypeContext($class, $type);
    }
}
