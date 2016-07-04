<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ResultProviderAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class ResultProviderAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ResultProviderAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ResultProviderAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new ResultProviderAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new ResultProviderAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Phpro\SoapClient\Type\ResultProviderInterface;

class MyType implements ResultProviderInterface
{

    /**
     * @return SomeClass|Phpro\SoapClient\Type\ResultInterface
     */
    public function getResult()
    {
        return \$this->prop1;
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
        $type = new Type('MyNamespace', 'MyType', [
            'prop1' => 'SomeClass',
        ]);

        return new TypeContext($class, $type);
    }
}
