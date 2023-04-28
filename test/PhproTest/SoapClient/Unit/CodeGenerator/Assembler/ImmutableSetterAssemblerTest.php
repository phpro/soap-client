<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ImmutableSetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\ImmutableSetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ImmutableSetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ImmutableSetterAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ImmutableSetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new ImmutableSetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new ImmutableSetterAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param string \$prop1
     * @return static
     */
    public function withProp1(string \$prop1) : static
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_doc_block_that_does_not_wrap()
    {
        $assembler = new ImmutableSetterAssembler();
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $generated = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1
     * @return static
     */
    public function withProp1(\This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1) : static
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    function it_assembles_with_no_doc_blocks()
    {
        $assembler = new ImmutableSetterAssembler((new ImmutableSetterAssemblerOptions())->withDocBlocks(false));
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $generated = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    public function withProp1(\This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1) : static
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    function it_assembles_with_no_type_hints()
    {
        $assembler = new ImmutableSetterAssembler((new ImmutableSetterAssemblerOptions())->withTypeHints(false));
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param string \$prop1
     * @return static
     */
    public function withProp1(\$prop1) : static
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_assembles_with_no_return_type(): void
    {
        $assembler = new ImmutableSetterAssembler(
            (new ImmutableSetterAssemblerOptions())
                ->withReturnTypes(false)
        );
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @param string \$prop1
     * @return static
     */
    public function withProp1(string \$prop1)
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_assembles_with_no_type_information(): void
    {
        $assembler = new ImmutableSetterAssembler(
            (new ImmutableSetterAssemblerOptions())
                ->withReturnTypes(false)
                ->withDocBlocks(false)
                ->withTypeHints(false)
        );
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    public function withProp1(\$prop1)
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_fluent_setter_with_advanced_types()
    {
        $assembler = new ImmutableSetterAssembler();
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
     * @param array<int<min,max>, string> \$prop1
     * @return static
     */
    public function withProp1(array \$prop1) : static
    {
        \$new = clone \$this;
        \$new->prop1 = \$prop1;

        return \$new;
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
        $type = new Type('MyNamespace', 'MyType', [
            $property = Property::fromMetaData('ns1', new MetaProperty('prop1', XsdType::guess('string'))),
        ]);

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $properties = [
            'prop1' => Property::fromMetaData(
                'This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever',
                new MetaProperty('prop1', XsdType::guess('Wrap'))
            ),
        ];
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', array_values($properties));
        $property = $properties['prop1'];
        return new PropertyContext($class, $type, $property);
    }
}
