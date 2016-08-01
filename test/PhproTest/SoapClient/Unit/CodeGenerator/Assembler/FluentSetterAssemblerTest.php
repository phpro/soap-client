<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssembler;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class SetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class FluentSetterAssemblerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new FluentSetterAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new FluentSetterAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new FluentSetterAssembler();
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
    public function setProp1(\$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
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
        $property = new Property('prop1', 'string');

        return new PropertyContext($class, $type, $property);
    }
}
