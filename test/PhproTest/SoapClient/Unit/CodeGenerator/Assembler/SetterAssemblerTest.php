<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\SetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\SetterAssemblerOptions;
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
class SetterAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new SetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new SetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new SetterAssembler((new SetterAssemblerOptions())->withTypeHints());
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param string \$prop1
     */
    public function setProp1(string \$prop1)
    {
        \$this->prop1 = \$prop1;
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
        $assembler = new SetterAssembler((new SetterAssemblerOptions())->withDocBlocks(false));
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
        $assembler = new SetterAssembler();
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $generated = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1
     */
    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
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
        $type = new Type('MyNamespace', 'MyType', [
            $property = new Property('prop1', 'string', 'ns1'),
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
