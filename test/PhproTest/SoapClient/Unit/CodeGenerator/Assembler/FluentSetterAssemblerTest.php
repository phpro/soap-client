<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
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
 * Class SetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class FluentSetterAssemblerTest extends TestCase
{
    use ConfigurationHelper;
    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new FluentSetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_property_context()
    {
        $assembler = new FluentSetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_fluent_setter()
    {
        $assembler = new FluentSetterAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param string \$prop1
     * @return \$this
     */
    public function setProp1(string \$prop1): static
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_property_without_type_hints()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withTypeHints(false));
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param string \$prop1
     * @return \$this
     */
    public function setProp1(\$prop1): static
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }


    #[Test]
    function it_assembles_with_no_doc_blocks()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withDocBlocks(false));
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    public function setProp1(string \$prop1): static
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    public function it_generates_without_return_types()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withReturnType(false));
        $context = $this->createContextWithAnUnknownType();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param \MyNamespace\\Foobar \$prop1
     * @return \$this
     */
    public function setProp1(\MyNamespace\Foobar \$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_doc_block_that_does_not_wrap()
    {
        $assembler = new FluentSetterAssembler();
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $generated = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1
     * @return \$this
     */
    public function setProp1(\This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1): static
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    function it_assembles_a_fluent_setter_with_advanced_types()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions()));
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            $property = Property::fromMetaData(
                $namespaces,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ], XsdType::create('MyType'));

        $context =  new PropertyContext($class, $type, $property);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param array<int<0,max>, string> \$prop1
     * @return \$this
     */
    public function setProp1(array \$prop1): static
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }
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
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            $property = Property::fromMetaData(
                $namespaces,
                new MetaProperty('prop1', XsdType::guess('string'))
            ),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithAnUnknownType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', [
            $property = Property::fromMetaData($namespaces, new MetaProperty('prop1', XsdType::guess('foobar'))),
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $longNamespaces = $this->createTypeNamespaceMap('This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever');
        $properties = [
            'prop1' => Property::fromMetaData(
                $longNamespaces,
                new MetaProperty('prop1', XsdType::guess('Wrap'))
            ),
        ];
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $type = new Type($namespaces, 'MyType', 'MyType', array_values($properties), XsdType::create('MyType'));
        $property = $properties['prop1'];
        return new PropertyContext($class, $type, $property);
    }
}
