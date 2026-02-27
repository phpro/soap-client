<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\SetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\SetterAssemblerOptions;
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
class SetterAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new SetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_property_context()
    {
        $assembler = new SetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_property()
    {
        $assembler = new SetterAssembler();
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
    public function setProp1(string \$prop1): void
    {
        \$this->prop1 = \$prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }


    #[Test]
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
    public function setProp1(string \$prop1): void
    {
        \$this->prop1 = \$prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_with_no_type_hints()
    {
        $assembler = new SetterAssembler((new SetterAssemblerOptions())->withTypeHints(false));
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
    public function setProp1(\$prop1): void
    {
        \$this->prop1 = \$prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
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
    public function setProp1(\This\Is\My\Very\Very\Long\Namespace\And\Class\Name\That\Should\Not\Never\Ever\Wrap \$prop1): void
    {
        \$this->prop1 = \$prop1;
    }
}

CODE;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    function it_assembles_a_setter_with_advanced_types()
    {
        $assembler = new SetterAssembler();
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
     * @param array<int<0,max>, string> \$prop1
     */
    public function setProp1(array \$prop1): void
    {
        \$this->prop1 = \$prop1;
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
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $ns1Context = $this->createCodeGeneratorContext('ns1');
        $type = new Type($context, 'MyType', 'MyType', [
            $property = Property::fromMetaData($ns1Context, new MetaProperty('prop1', XsdType::guess('string'))),
        ], XsdType::create('MyType'));

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
