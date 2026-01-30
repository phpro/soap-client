<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\JsonSerializableAssembler;
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
 * Class JsonSerializableAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class JsonSerializableAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new JsonSerializableAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new JsonSerializableAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_type()
    {
        $assembler = new JsonSerializableAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use JsonSerializable;

class MyType implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'prop1' => \$this->prop1,
            'prop2' => \$this->prop2,
        ];
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
