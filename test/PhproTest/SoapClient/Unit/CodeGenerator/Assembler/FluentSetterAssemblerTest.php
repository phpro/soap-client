<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\FluentSetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class SetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class FluentSetterAssemblerTest extends TestCase
{
    /**
     * @param string $version
     * @return bool
     */
    function zendOlderOrEqual($version)
    {
        $zendCodeVersion = \PackageVersions\Versions::getVersion('zendframework/zend-code');
        $zendCodeVersion = substr($zendCodeVersion, 0, strpos($zendCodeVersion, '@'));

        return version_compare($zendCodeVersion, $version, '>=');
    }

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new FluentSetterAssembler(new FluentSetterAssemblerOptions());
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new FluentSetterAssembler(new FluentSetterAssemblerOptions());
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new FluentSetterAssembler(new FluentSetterAssemblerOptions());
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param string \$prop1
     * @return \$this
     */
    public function setProp1(string \$prop1)
    {
        \$this->prop1 = \$prop1;
        return \$this;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_property_with_an_unkown_type()
    {
        $assembler = new FluentSetterAssembler(new FluentSetterAssemblerOptions());
        $context = $this->createContextWithAnUnknownType();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param \\MyNamespace\\foobar \$prop1
     * @return \$this
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
     * @test
     */
    public function it_generates_return_types()
    {
        if (!$this->zendOlderOrEqual('3.3.0')) {
            $this->markTestSkipped('zend-code not new enough');
        }
        $assembler = new FluentSetterAssembler((new FluentSetterAssemblerOptions())->withReturnType());
        $context = $this->createContextWithAnUnknownType();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @param \MyNamespace\\foobar \$prop1
     * @return \$this
     */
    public function setProp1(\$prop1) : \MyNamespace\MyType
    {
        \$this->prop1 = \$prop1;
        return \$this;
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
        $type = new Type('MyNamespace', 'MyType', [
            'prop1' => 'string',
        ]);
        $property = new Property('prop1', 'string', 'ns1');

        return new PropertyContext($class, $type, $property);
    }

    /**
     * @return PropertyContext
     */
    private function createContextWithAnUnknownType()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', [
            'prop1' => 'foobar',
        ]);
        $property = new Property('prop1', 'foobar', 'MyNamespace');

        return new PropertyContext($class, $type, $property);
    }
}
