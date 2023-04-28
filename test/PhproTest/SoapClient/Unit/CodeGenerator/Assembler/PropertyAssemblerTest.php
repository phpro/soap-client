<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class PropertyAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class PropertyAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new PropertyAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_assembles_property_without_default_value()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Type specific docs
     *
     * @var string
     */
    private string \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_with_visibility_without_default_value()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withVisibility(PropertyGenerator::VISIBILITY_PUBLIC)
        );
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Type specific docs
     *
     * @var string
     */
    public string \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_without_doc_blocks()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withDocBlocks(false)
        );
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    private string \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_with_visibility_without_type_info()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withTypeHints(false)
        );
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Type specific docs
     *
     * @var string
     */
    private \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_doc_block_that_does_not_wrap()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @var \\This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever\\Wrap
     */
    private \\This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever\\Wrap \$prop1;
}

CODE;
        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_properties_with_advanced_types()
    {
        $assembler = new PropertyAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            $property = Property::fromMetaData(
                $namespace,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ]);

        $context =  new PropertyContext($class, $type, $property);
        $assembler->assemble($context);
        $code = $context->getClass()->generate();

        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @var array<int<min,max>, string>
     */
    private array \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }


    /**
     * @return PropertyContext
     */
    private function createContext()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [
            $property = Property::fromMetaData('ns1', new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withDocs('Type specific docs')
            ))),
        ]);

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [
            $property = Property::fromMetaData(
                'This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever',
                new MetaProperty('prop1', XsdType::guess('Wrap'))
            ),
        ]);
        return new PropertyContext($class, $type, $property);
    }
}
