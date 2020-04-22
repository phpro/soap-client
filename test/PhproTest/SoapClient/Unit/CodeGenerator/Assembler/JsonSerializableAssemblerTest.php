<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\JsonSerializableAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\ClassGenerator;

/**
 * Class JsonSerializableAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class JsonSerializableAssemblerTest extends TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new JsonSerializableAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new JsonSerializableAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
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

    /**
     * @return array
     */
    public function jsonSerialize()
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
        $type = new Type($namespace = 'MyNamespace', 'MyType', [
            new Property('prop1', 'array', $namespace),
            new Property('prop2', 'array', $namespace),
        ]);

        return new TypeContext($class, $type);
    }
}
