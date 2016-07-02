<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\ConstructorAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class ConstructorAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ConstructorAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new ConstructorAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new ConstructorAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * Constructor
     *
     * @var string \$prop1
     * @var int \$prop2
     */
    public function __construct(\$prop1, \$prop2)
    {
        \$this->prop1 = \$prop1
        \$this->prop2 = \$prop2
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
            'prop1' => 'string',
            'prop2' => 'int'
        ]);

        return new TypeContext($class, $type);
    }
}
