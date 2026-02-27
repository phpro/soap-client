<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssemblerOptions;
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
 * Class GetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class GetterAssemblerTest extends TestCase
{
    use ConfigurationHelper;
    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new GetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_property_context()
    {
        $assembler = new GetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
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
    public function getProp1(): string
    {
        return \$this->prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_an_optional_value()
    {
        $assembler = new GetterAssembler(GetterAssemblerOptions::create()->withOptionalValue());
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @return null | string
     */
    public function getProp1(): ?string
    {
        return \$this->prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
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

    #[Test]
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
    public function getProp2(): int
    {
        return \$this->prop2;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
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
    public function isProp3(): bool
    {
        return \$this->prop3;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
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
    public function getProp4(): \\ns1\\MyResponse
    {
        return \$this->prop4;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
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
    public function getProp1(): \This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap
    {
        return \$this->prop1;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    function it_assembles_a_property_with_advanced_types()
    {
        $assembler = new GetterAssembler();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $codeGenContext = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($codeGenContext, 'MyType', 'MyType', [
            $property = Property::fromMetaData(
                $codeGenContext,
                new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                ))
            ),
        ], XsdType::create('MyType'));

        $context =  new PropertyContext($class, $type, $property, $codeGenContext);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{
    /**
     * @return array<int<0,max>, string>
     */
    public function getProp1(): array
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
        $ns1Context = $this->createCodeGeneratorContext('ns1');
        $properties = [
            'prop1' => Property::fromMetaData($ns1Context, new MetaProperty('prop1', XsdType::guess('string'))),
            'prop2' => Property::fromMetaData($ns1Context, new MetaProperty('prop2', XsdType::guess('int'))),
            'prop3' => Property::fromMetaData($ns1Context, new MetaProperty('prop3', XsdType::guess('bool'))),
            'prop4' => Property::fromMetaData($ns1Context, new MetaProperty('prop4', XsdType::guess('My_Response'))),
        ];

        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', array_values($properties), XsdType::create('MyType'));
        $property = $properties[$propertyName];

        return new PropertyContext($class, $type, $property, $context);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithLongType()
    {
        $longContext = $this->createCodeGeneratorContext('This\\Is\\My\\Very\\Very\\Long\\Namespace\\And\\Class\\Name\\That\\Should\\Not\\Never\\Ever');
        $properties = [
            'prop1' => Property::fromMetaData(
                $longContext,
                new MetaProperty('prop1', XsdType::guess('Wrap'))
            ),
        ];
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', array_values($properties), XsdType::create('MyType'));
        $property = $properties['prop1'];
        return new PropertyContext($class, $type, $property, $context);
    }
}
