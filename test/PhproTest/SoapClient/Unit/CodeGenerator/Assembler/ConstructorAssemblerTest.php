<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ConstructorAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\ConstructorAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Config\DefaultValuesStrategy;
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
 * Class ConstructorAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ConstructorAssemblerTest extends TestCase
{
    use ConfigurationHelper;
    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new ConstructorAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new ConstructorAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
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

    #[Test]
    function it_assambles_a_constructor()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('int'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop3', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

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

    #[Test]
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

    #[Test]
    function it_assambles_a_constructor_with_advanced_types()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData(
                $namespaces,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ], XsdType::create('MyType'));

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
     * @param array<int<0,max>, string> \$prop1
     */
    public function __construct(array \$prop1)
    {
        \$this->prop1 = \$prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_constructor_with_defaults_disabled()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())->withDefaultValues(DefaultValuesStrategy::None)
        );
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('int'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop3', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
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

    #[Test]
    function it_assembles_constructor_reordering_defaults_to_end()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('name', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('contact', XsdType::guess('Contact')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
            Property::fromMetaData($namespaces, new MetaProperty('address', XsdType::guess('Address'))),
            Property::fromMetaData($namespaces, new MetaProperty('note', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param string \$name
     * @param \MyNamespace\Address \$address
     * @param null | \MyNamespace\Contact \$contact
     * @param null | string \$note
     */
    public function __construct(string \$name, \MyNamespace\Address \$address, ?\MyNamespace\Contact \$contact = null, ?string \$note = null)
    {
        \$this->name = \$name;
        \$this->address = \$address;
        \$this->contact = \$contact;
        \$this->note = \$note;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_constructor_with_nullable_complex_type()
    {
        $assembler = new ConstructorAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('address', XsdType::guess('Address'))),
            Property::fromMetaData($namespaces, new MetaProperty('contact', XsdType::guess('Contact')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param \MyNamespace\Address \$address
     * @param null | \MyNamespace\Contact \$contact
     */
    public function __construct(\MyNamespace\Address \$address, ?\MyNamespace\Contact \$contact = null)
    {
        \$this->address = \$address;
        \$this->contact = \$contact;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_constructor_without_type_hints_skips_defaults()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())->withTypeHints(false)->withDefaultValues(DefaultValuesStrategy::All)
        );
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
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
     * @param \MyNamespace\SomeClass \$prop2
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

    #[Test]
    function it_assembles_constructor_with_optional_value()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())->withOptionalValue()
        );
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param null | string \$prop1
     * @param null | \MyNamespace\SomeClass \$prop2
     */
    public function __construct(?string \$prop1 = null, ?\MyNamespace\SomeClass \$prop2 = null)
    {
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_constructor_with_optional_value_and_defaults_disabled()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())->withOptionalValue()->withDefaultValues(DefaultValuesStrategy::None)
        );
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param null | string \$prop1
     * @param null | \MyNamespace\SomeClass \$prop2
     */
    public function __construct(?string \$prop1 = null, ?\MyNamespace\SomeClass \$prop2 = null)
    {
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_constructor_with_all_defaults()
    {
        $assembler = new ConstructorAssembler(
            (new ConstructorAssemblerOptions())->withDefaultValues(DefaultValuesStrategy::All)
        );
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('int'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop3', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        $context = new TypeContext($class, $type);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * Constructor
     *
     * @param \MyNamespace\SomeClass \$prop3
     * @param string \$prop1
     * @param int \$prop2
     */
    public function __construct(\MyNamespace\SomeClass \$prop3, string \$prop1 = '', int \$prop2 = 0)
    {
        \$this->prop3 = \$prop3;
        \$this->prop1 = \$prop1;
        \$this->prop2 = \$prop2;
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
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($namespaces, new MetaProperty('prop2', XsdType::guess('int'))),
        ], XsdType::create('MyType'));

        return new TypeContext($class, $type);
    }
}
