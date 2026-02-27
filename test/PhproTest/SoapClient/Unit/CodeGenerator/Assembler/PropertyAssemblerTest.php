<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
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
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new PropertyAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_assembles_property()
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

    #[Test]
    function it_assembles_property_with_default_value()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withOptionalValue()
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
     * @var null | string
     */
    private ?string \$prop1 = null;
}

CODE;

        $this->assertEquals($expected, $code);
    }


    #[Test]
    function it_assembles_with_visibility()
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    function it_assembles_properties_with_advanced_types()
    {
        $assembler = new PropertyAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData(
                $context,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ], XsdType::create('MyType'));

        $propertyContext =  new PropertyContext($class, $type, $property, $context);
        $assembler->assemble($propertyContext);
        $code = $propertyContext->getClass()->generate();

        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @var array<int<0,max>, string>
     */
    private array \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_overwrite_props_during_assembling()
    {
        $context = $this->createContext();

        $assembler1 = new PropertyAssembler();
        $assembler1->assemble($context);

        $assembler2 = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withVisibility(PropertyGenerator::VISIBILITY_PUBLIC)
        );
        $assembler2->assemble($context);

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

    #[Test]
    function it_assembles_property_with_null()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()
        );
        $context = $this->createContextWithNullableType();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Type specific docs
     *
     * @var null | string
     */
    private ?string \$prop1 = null;
}

CODE;

        $this->assertEquals($expected, $code);
    }


    #[Test]
    function it_assembles_property_without_defaults()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withDefaultValues(DefaultValuesStrategy::None)
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
    private string \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_property_with_defaults_and_optional_value()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withDefaultValues()->withOptionalValue()
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
     * @var null | string
     */
    private ?string \$prop1 = null;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_nullable_complex_type_with_defaults()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContextWithNullableComplexType();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @var null | \MyNamespace\SomeClass
     */
    private ?\MyNamespace\SomeClass \$prop1 = null;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_non_nullable_complex_type_without_default()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContextWithComplexType();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @var \MyNamespace\SomeClass
     */
    private \MyNamespace\SomeClass \$prop1;
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_property_with_all_defaults()
    {
        $assembler = new PropertyAssembler(
            PropertyAssemblerOptions::create()->withDefaultValues(DefaultValuesStrategy::All)
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
    private string \$prop1 = '';
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
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withDocs('Type specific docs')
            ))),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property, $context);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData(
                $context,
                new MetaProperty('prop1', XsdType::guess('Wrap'))
            ),
        ], XsdType::create('MyType'));
        return new PropertyContext($class, $type, $property, $context);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithNullableType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withDocs('Type specific docs')->withIsNullable(true)
            ))),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property, $context);
    }

    private function createContextWithNullableComplexType(): PropertyContext
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('SomeClass')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property, $context);
    }

    private function createContextWithComplexType(): PropertyContext
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property, $context);
    }
}
