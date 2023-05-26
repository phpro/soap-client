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
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

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
    public function getProp1() : string
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
    public function it_assembles_without_return_type()
    {
        $options = (new GetterAssemblerOptions())
            ->withReturnType(false);
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
    public function isProp3() : bool
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
    public function getProp1() : \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap
    {
        return \$this->prop1;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    function it_assembles_a_property_with_advanced_types()
    {
        $assembler = new GetterAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            $property = Property::fromMetaData(
                $namespace,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ], new TypeMeta());

        $context =  new PropertyContext($class, $type, $property);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @return array<int<min,max>, string>
     */
    public function getProp1() : array
    {
        return \$this->prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @param string $propertyName
     * @return PropertyContext
     */
    private function createContext($propertyName = 'prop1')
    {
        $properties = [
            'prop1' => Property::fromMetaData('ns1', new MetaProperty('prop1', XsdType::guess('string'))),
            'prop2' => Property::fromMetaData('ns1', new MetaProperty('prop2', XsdType::guess('int'))),
            'prop3' => Property::fromMetaData('ns1', new MetaProperty('prop3', XsdType::guess('boolean'))),
            'prop4' => Property::fromMetaData('ns1', new MetaProperty('prop4', XsdType::guess('My_Response'))),
        ];

        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', array_values($properties), new TypeMeta());
        $property = $properties[$propertyName];

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
        $type = new Type('MyNamespace', 'MyType', array_values($properties), new TypeMeta());
        $property = $properties['prop1'];
        return new PropertyContext($class, $type, $property);
    }
}
