<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ResultProviderAssembler;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\Type\MixedResult;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Laminas\Code\Generator\ClassGenerator;
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

/**
 * Class ResultProviderAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ResultProviderAssemblerTest extends TestCase
{
    use ConfigurationHelper;
    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new ResultProviderAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_type_context()
    {
        $assembler = new ResultProviderAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_type()
    {
        $assembler = new ResultProviderAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Type\ResultInterface;

class MyType implements ResultProviderInterface
{
    /**
     * @return \MyNamespace\SomeClass|ResultInterface
     */
    public function getResult(): \Phpro\SoapClient\Type\ResultInterface
    {
        return \$this->prop1;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_type_with_wrapper_class()
    {
        $assembler = new ResultProviderAssembler(MixedResult::class);
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Type\MixedResult;

class MyType implements ResultProviderInterface
{
    /**
     * @return MixedResult
     */
    public function getResult(): \Phpro\SoapClient\Type\ResultInterface
    {
        return new MixedResult(\$this->prop1);
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_assembles_a_type_with_wrapper_class_with_prefixed_slash()
    {
        $assembler = new ResultProviderAssembler('\\' . MixedResult::class);
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use Phpro\SoapClient\Type\ResultProviderInterface;
use Phpro\SoapClient\Type\MixedResult;

class MyType implements ResultProviderInterface
{
    /**
     * @return MixedResult
     */
    public function getResult(): \Phpro\SoapClient\Type\ResultInterface
    {
        return new MixedResult(\$this->prop1);
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
        $context = $this->createCodeGeneratorContext('MyNamespace');
        $type = new Type($context, 'MyType', 'MyType', [
            Property::fromMetaData($context, new MetaProperty('prop1', XsdType::guess('SomeClass'))),
        ], XsdType::create('MyType'));

        return new TypeContext($class, $type, $context);
    }
}
