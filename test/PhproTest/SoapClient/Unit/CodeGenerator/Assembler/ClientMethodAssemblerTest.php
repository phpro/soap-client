<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Assembler\ClientMethodAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\Exception\AssemblerException;
use PHPUnit\Framework\TestCase;

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
        $class->setNamespaceName($namespace = 'Vendor\\MyNamespace');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [
                new Parameter('param', $typeNamespace.'\\ParamType'),
            ],
            'ReturnType',
            $typeNamespace
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
        $class->setNamespaceName($namespace = 'Vendor\\MyNamespace');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [
                new Parameter('param', $typeNamespace.'\\ParamType'),
                new Parameter('param2', $typeNamespace.'\\OtherParamType'),
            ],
            'ReturnType',
            $typeNamespace
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
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\RequestInterface;
use Vendor\MyTypeNamespace;

class  extends \Phpro\SoapClient\Client
{

    /**
     * @param RequestInterface|MyTypeNamespace\ParamType \$param
     * @return ResultInterface|MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function functionName(\Vendor\MyTypeNamespace\ParamType \$param) : \Vendor\MyTypeNamespace\ReturnType
    {
        return \$this->call('functionName', \$param);
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
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\ResultInterface;
use Vendor\MyTypeNamespace;

class  extends \Phpro\SoapClient\Client
{

    /**
     * MultiArgumentRequest with following params:
     *
     * Vendor\MyTypeNamespace\ParamType \$param
     * Vendor\MyTypeNamespace\OtherParamType \$param2
     *
     * @param Phpro\SoapClient\Type\MultiArgumentRequest
     * @return ResultInterface|MyTypeNamespace\ReturnType
     */
    public function functionName(\Phpro\SoapClient\Type\MultiArgumentRequest \$multiArgumentRequest) : \Vendor\MyTypeNamespace\ReturnType
    {
        return \$this->call('functionName', \$multiArgumentRequest);
    }


}

CODE;
        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_method_with_underscore_param_type()
    {
        $assembler = new ClientMethodAssembler();
        $class = new ClassGenerator();
        $class->setNamespaceName('Vendor\\MyNamespace');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'Function_name',
            [
                new Parameter('param', $typeNamespace.'\\param_type'),
            ],
            'return_type',
            $typeNamespace
        );

        $context = new ClientMethodContext($class, $method);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\RequestInterface;
use Vendor\MyTypeNamespace;

class  extends \Phpro\SoapClient\Client
{

    /**
     * @param RequestInterface|MyTypeNamespace\ParamType \$param
     * @return ResultInterface|MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function function_name(\Vendor\MyTypeNamespace\ParamType \$param) : \Vendor\MyTypeNamespace\ReturnType
    {
        return \$this->call('Function_name', \$param);
    }


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_throws_an_exception_when_wrong_context_is_passed() {
        $clientMethodAssembler = new ClientMethodAssembler();
        $context = $this->createMock(ClientContext::class);
        $this->expectException(AssemblerException::class);
        $this->expectExceptionMessage(sprintf(
                'Phpro\SoapClient\CodeGenerator\Assembler\ClientMethodAssembler::assemble '.
                'expects an Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext as input %s given',
                get_class($context)
            )
        );
        $clientMethodAssembler->assemble($context);
    }
}
