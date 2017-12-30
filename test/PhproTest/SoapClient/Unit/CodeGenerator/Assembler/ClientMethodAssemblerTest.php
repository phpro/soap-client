<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\ClientMethodAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class GetterAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ClientMethodAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ClientMethodAssembler();
        $this->assertInstanceOf(ClientMethodAssembler::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_client_method_context()
    {
        $assembler = new ClientMethodAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @return ClientMethodContext
     */
    private function createContext()
    {
        // ClassGenerator $class, ClientMethod $method
        $class = new ClassGenerator();
        $class->setNamespaceName('MyNamespace');
        $method = ClientMethod::createFromExtSoapFunctionString(
            'ReturnType functionName(ParamType $param)',
            'MyTypeNamespace'
        );

        return new ClientMethodContext($class, $method);
    }

    /**
     * @test
     */
    function it_assembles_a_method()
    {
        $assembler = new ClientMethodAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class  extends \Phpro\SoapClient\Client
{

    /**
     * @param \Phpro\SoapClient\Type\RequestInterface|\MyTypeNamespace\ParamType \$ParamType
     * @return \Phpro\SoapClient\Type\ResultInterface
     * @throws \Phpro\SoapClient\Exception\SoapException
     */
    public function functionName(\MyTypeNamespace\ParamType \$ParamType) : \MyTypeNamespace\ReturnType
    {
        return \$this->call('ParamType', \$ParamType);
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }
}
