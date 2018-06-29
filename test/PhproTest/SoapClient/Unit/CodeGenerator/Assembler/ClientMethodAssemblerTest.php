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
     * @return ClientMethodContext
     */
    private function createMultiArgumentContext()
    {
        // ClassGenerator $class, ClientMethod $method
        $class = new ClassGenerator();
        $class->setNamespaceName('MyNamespace');
        $method = ClientMethod::createFromExtSoapFunctionString(
            'ReturnType functionName(ParamType $param, OtherParamType $param2)',
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

    public function functionName(\MyTypeNamespace\ParamType \$param) : \MyTypeNamespace\ReturnType
    {
        return \$this->call('ParamType', \$param);
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_multiargumentrequests()
    {
        $assembler = new ClientMethodAssembler();
        $context = $this->createMultiArgumentContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class  extends \Phpro\SoapClient\Client
{

    /**
     * MultiArgumentRequest with following params:
     *
     * MyTypeNamespace\ParamType \$param
     * MyTypeNamespace\OtherParamType \$param2
     *
     * @param Phpro\SoapClient\Type\MultiArgumentRequest
     * @return ReturnType
     */
    public function functionName(\Phpro\SoapClient\Type\MultiArgumentRequest \$multiArgumentRequest) : \MyTypeNamespace\ReturnType
    {
        return \$this->call('MultiArgumentRequest', \$multiArgumentRequest);
    }


}

CODE;
        $this->assertEquals($expected, $code);
    }
}
