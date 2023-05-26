<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\IteratorAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use function Psl\Fun\identity;

/**
 * Class IteratorAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class IteratorAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new IteratorAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new IteratorAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new IteratorAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use IteratorAggregate;

/**
 * @phpstan-implements \IteratorAggregate<int<1,2>, string>
 * @psalm-implements \IteratorAggregate<int<1,2>, string>
 */
class MyType implements IteratorAggregate
{
    /**
     * @return \ArrayIterator|string[]
     * @phpstan-return \ArrayIterator<int<1,2>, string>
     * @psalm-return \ArrayIterator<int<1,2>, string>
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator(\$this->prop1);
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_type_with_no_occurs_information()
    {
        $assembler = new IteratorAssembler();
        $context = $this->createContext(
            static fn (TypeMeta $meta): TypeMeta => $meta
                ->withMaxOccurs(null)
                ->withMinOccurs(null)
        );
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use IteratorAggregate;

/**
 * @phpstan-implements \IteratorAggregate<int<min,max>, string>
 * @psalm-implements \IteratorAggregate<int<min,max>, string>
 */
class MyType implements IteratorAggregate
{
    /**
     * @return \ArrayIterator|string[]
     * @phpstan-return \ArrayIterator<int<min,max>, string>
     * @psalm-return \ArrayIterator<int<min,max>, string>
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator(\$this->prop1);
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @return TypeContext
     */
    private function createContext(?callable $metaConfigurator = null)
    {
        $metaConfigurator ??= identity();
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            Property::fromMetaData($namespace, new MetaProperty('prop1', XsdType::guess('string')->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $metaConfigurator($meta
                    ->withIsList(true)
                    ->withMinOccurs(1)
                    ->withMaxOccurs(2)
            )))),
        ], new TypeMeta());

        return new TypeContext($class, $type);
    }
}
