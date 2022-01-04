<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\IteratorAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;

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
 * @phpstan-implements \IteratorAggregate<array-key, array>
 * @psalm-implements \IteratorAggregate<array-key, array>
 */
class MyType implements IteratorAggregate
{

    /**
     * @return \ArrayIterator|array[]
     * @phpstan-return \ArrayIterator<array-key, array>
     * @psalm-return \ArrayIterator<array-key, array>
     */
    public function getIterator()
    {
        return new \ArrayIterator(is_array(\$this->prop1) ? \$this->prop1 : []);
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
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            new Property('prop1', 'array', $namespace),
        ]);

        return new TypeContext($class, $type);
    }
}
