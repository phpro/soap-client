<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class GetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class GetterAssemblerTest extends TestCase
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
        $assembler = new GetterAssembler(new GetterAssemblerOptions());
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new GetterAssembler(new GetterAssemblerOptions());
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_property()
    {
        $assembler = new GetterAssembler(new GetterAssemblerOptions());
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return string
     */
    public function getProp1()
    {
        return \$this->prop1;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    public function it_assembles_with_return_type()
    {
        if (!$this->zendOlderOrEqual('3.3.0')) {
            $this->markTestSkipped('zendframework/zend-code 3.3.0 required');
        }
        $options = (new GetterAssemblerOptions())
            ->withReturnType();
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop2');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return int
     */
    public function getProp2() : int
    {
        return \$this->prop2;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_property_methodnames_correctly()
    {
        $options = (new GetterAssemblerOptions())->withBoolGetters();
        $assembler = new GetterAssembler($options);

        $context = $this->createContext('prop3');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{

    /**
     * @return bool
     */
    public function isProp3()
    {
        return \$this->prop3;
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @param string $propertyName
     * @return PropertyContext
     */
    private function createContext($propertyName = 'prop1')
    {
        $properties = [
            'prop1' => 'string',
            'prop2' => 'int',
            'prop3' => 'boolean',
        ];

        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', $properties);
        $property = new Property($propertyName, $properties[$propertyName], 'ns1');

        return new PropertyContext($class, $type, $property);
    }
}
