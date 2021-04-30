<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class SetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class FluentSetterAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new FluentSetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new FluentSetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withTypeHints());
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
    public function setProp1(string \$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
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
    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }


    /**
     * @test
     */
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

    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_property_with_an_unkown_type()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withTypeHints());
        $context = $this->createContextWithAnUnknownType();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param \\MyNamespace\\Foobar \$prop1
     * @return \$this
     */
    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_generates_return_types()
    {
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withReturnType());
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
    public function setProp1(\$prop1) : \MyNamespace\MyType
    {
        \$this->prop1 = \$prop1;
        return \$this;
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
    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @return PropertyContext
     */
    private function createContext()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            $property = new Property('prop1', 'string', 'ns1'),
        ]);

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithAnUnknownType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            $property = new Property('prop1', 'foobar', $namespace),
        ]);

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $properties = [
            'prop1' => new Property(
                'prop1',
                'Wrap',
                'This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever'
            ),
        ];
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', array_values($properties));
        $property = $properties['prop1'];
        return new PropertyContext($class, $type, $property);
    }
}
