<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyDefaultsAssembler;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as EngineProperty;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

class PropertyDefaultsAssemblerTest extends TestCase
{
    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new PropertyDefaultsAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[DataProvider('provideAssemblerContexts')]
    #[Test]
    function it_can_enhance_assembled_property_with_a_default_value(
        PropertyContext $context,
        string $expectedCode,
        bool $skipPropertyGeneration = false,
    ): void {
        if (!$skipPropertyGeneration) {
            (new PropertyAssembler(PropertyAssemblerOptions::create()->withDocBlocks(false)))->assemble($context);
        }
        (new PropertyDefaultsAssembler())->assemble($context);

        $code = $context->getClass()->generate();
        $this->assertEquals($expectedCode, $code);
    }

    public static function provideAssemblerContexts(): iterable
    {
        $expectedOutput = <<<EOCODE
namespace MyNamespace;

class MyType
{
    %s
}

EOCODE;

        yield 'mixed' => [
            self::createContext(self::configureProperty(XsdType::create('mixed'))),
            sprintf($expectedOutput, 'private mixed $prop1 = null;')
        ];
        yield 'string' => [
            self::createContext(self::configureProperty(XsdType::create('string'))),
            sprintf($expectedOutput, 'private string $prop1 = \'\';')
        ];
        yield 'int' => [
            self::createContext(self::configureProperty(XsdType::create('int'))),
            sprintf($expectedOutput, 'private int $prop1 = 0;')
        ];
        yield 'bool' => [
            self::createContext(self::configureProperty(XsdType::create('bool'))),
            sprintf($expectedOutput, 'private bool $prop1 = false;')
        ];
        yield 'float' => [
            self::createContext(self::configureProperty(XsdType::create('float'))),
            sprintf($expectedOutput, 'private float $prop1 = 0;')
        ];
        yield 'nullable-type' => [
            self::createContext(self::configureProperty(XsdType::create('SomeClass')->withMeta(
                static fn(TypeMeta $meta): TypeMeta => $meta->withIsNullable(true)
            ))),
            sprintf($expectedOutput, 'private ?\ns1\SomeClass $prop1 = null;')
        ];
        yield 'non-nullable-type' => [
            self::createContext(self::configureProperty(XsdType::create('SomeClass'))),
            sprintf($expectedOutput, 'private \ns1\SomeClass $prop1;')
        ];
        yield 'without-known-property' => [
            self::createContext(self::configureProperty(XsdType::create('SomeClass'))),
            <<<EOCODE
namespace MyNamespace;

class MyType
{
}

EOCODE,
            true
        ];
    }

    private static function configureProperty(XsdType $type): Property
    {
        $namespaces = TypeNamespaceMap::create(new Destination('/generated', 'ns1'));
        return Property::fromMetaData($namespaces, new MetaProperty('prop1', $type));
    }

    private static function createContext(Property $property): PropertyContext
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $namespaces = TypeNamespaceMap::create(new Destination('/generated', 'MyNamespace'));
        $type = new Type($namespaces, 'MyType', 'MyType', [
            $property
        ], XsdType::create('MyType'));

        return new PropertyContext($class, $type, $property);
    }
}
