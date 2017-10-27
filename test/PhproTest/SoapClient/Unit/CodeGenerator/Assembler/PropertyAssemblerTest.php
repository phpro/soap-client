<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\PropertyAssembler;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Class PropertyAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class PropertyAssemblerTest extends TestCase
{
    function show_null() {
        $zendCodeVersion = \PackageVersions\Versions::getVersion('zendframework/zend-code');
        $zendCodeVersion = substr($zendCodeVersion, 0, strpos($zendCodeVersion, '@'));
        return version_compare($zendCodeVersion, '3.3.0', '<');
    }

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new PropertyAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new PropertyAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        if (!$this->show_null()) {
            $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @var string
     */
    private \$prop1;


}

CODE;
        } else {
            $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @var string
     */
    private \$prop1 = null;


}

CODE;

        }

        $this->assertEquals($expected, $code);
    }


    /**
     * @test
     */
    function it_assembles_with_visibility()
    {
        $assembler = new PropertyAssembler(PropertyGenerator::VISIBILITY_PUBLIC);
        $context = $this->createContext();
        $assembler->assemble($context);
        $code = $context->getClass()->generate();
        if (!$this->show_null()) {
            $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @var string
     */
    public \$prop1;


}

CODE;
        }else{
            $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @var string
     */
    public \$prop1 = null;


}

CODE;
        }

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
            'prop2' => 'int',
        ]);
        $property = new Property('prop1', 'string');

        return new PropertyContext($class, $type, $property);
    }
}
