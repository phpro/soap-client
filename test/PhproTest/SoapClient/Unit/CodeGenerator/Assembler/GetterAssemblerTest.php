<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class GetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class GetterAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new GetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new GetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new GetterAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return string
     */
    public function getProp1()
    {
        return \$this->prop1;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_assembles_with_return_type()
    {
        $options = (new GetterAssemblerOptions())
            ->withReturnType();
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop2');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return int
     */
    public function getProp2() : int
    {
        return \$this->prop2;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_assembles_with_no_doc_blocks()
    {
        $options = (new GetterAssemblerOptions())
            ->withDocBlocks(false);
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop2');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    public function getProp2()
    {
        return \$this->prop2;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_property_methodnames_correctly()
    {
        $options = (new GetterAssemblerOptions())->withBoolGetters();
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop3');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return bool
     */
    public function isProp3()
    {
        return \$this->prop3;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_with_normalised_class_name()
    {
        $options = (new GetterAssemblerOptions())->withReturnType();
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop4');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return \\ns1\\MyResponse
     */
    public function getProp4() : \\ns1\\MyResponse
    {
        return \$this->prop4;
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
        $assembler = new GetterAssembler();
        $context = $this->createContextWithLongType();

        $assembler->assemble($context);

        $generated = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap
     */
    public function getProp1()
    {
        return \$this->prop1;
    }


}

CODE;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @param string $propertyName
     * @return PropertyContext
     */
    private function createContext($propertyName = 'prop1')
    {
        $properties = [
            'prop1' => new Property('prop1', 'string', 'ns1'),
            'prop2' => new Property('prop2', 'int', 'ns1'),
            'prop3' => new Property('prop3', 'boolean', 'ns1'),
            'prop4' => new Property('prop4', 'My_Response', 'ns1'),
        ];

        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', array_values($properties));
        $property = $properties[$propertyName];

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
