<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\RequestAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class RequestAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class RequestAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new RequestAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new RequestAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_type()
    {
        $assembler = new RequestAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Phpro\SoapClient\Type\RequestInterface;

class MyType implements RequestInterface
{
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
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('string'))),
            Property::fromMetaData($context, new MetaProperty('prop2', XsdType::guess('int'))),
        ], XsdType::create('MyType'));

        return new TypeContext($class, $type, $context);
    }
}
