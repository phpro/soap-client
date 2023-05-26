<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ConstructorAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\ConstructorAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ConstructorAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ConstructorAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ConstructorAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new ConstructorAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type_without_type_hints()
    {
        $assembler = new ConstructorAssembler((new ConstructorAssemblerOptions())->withTypeHints(false));
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param string \$prop1
     * @param int \$prop2
     */
    public function __construct(\$prop1, \$prop2)
    {
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assambles_a_constructor()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            Property::fromMetaData($namespace, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespace, new MetaProperty('prop2', XsdType::guess('int'))),
            Property::fromMetaData($namespace, new MetaProperty('prop3', XsdType::guess('SomeClass'))),
        ]);

        $context =  new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param string \$prop1
     * @param int \$prop2
     * @param \MyNamespace\SomeClass \$prop3
     */
    public function __construct(string \$prop1, int \$prop2, \MyNamespace\SomeClass \$prop3)
    {
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
        \$this->prop3 = \$prop3;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_type_with_no_doc_blocks()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())
                ->withDocBlocks(false)
                ->withTypeHints(true)
        );
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    public function __construct(string \$prop1, int \$prop2)
    {
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assambles_a_constructor_with_advanced_types()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            Property::fromMetaData(
                $namespace,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ]);

        $context =  new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param array<int<min,max>, string> \$prop1
     */
    public function __construct(array \$prop1)
    {
        \$this->prop1 = \$prop1;
    }
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
        ]);

        return new TypeContext($class, $type);
    }
}
