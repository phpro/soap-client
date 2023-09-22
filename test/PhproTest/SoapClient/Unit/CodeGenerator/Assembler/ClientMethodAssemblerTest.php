<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Assembler\ClientMethodAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Model\ReturnType;
use Phpro\SoapClient\Exception\AssemblerException;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

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
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [
                new Parameter('param', 'ParamType', $typeNamespace, new TypeMeta()),
            ],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('ReturnType')),
            $typeNamespace,
            (new MethodMeta())->withDocs('This is an awesome function.')
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
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [
                new Parameter('param', 'ParamType', $typeNamespace, new TypeMeta()),
                new Parameter('param2', 'OtherParamType', $typeNamespace, new TypeMeta()),
            ],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('ReturnType')),
            $typeNamespace,
            (new MethodMeta())->withDocs('This is an awesome function.')
        );

        return new ClientMethodContext($class, $method);
    }

    /**
     * @return ClientMethodContext
     */
    private function createNoParamsContext()
    {
        // ClassGenerator $class, ClientMethod $method
        $class = new ClassGenerator();
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('ReturnType')),
            $typeNamespace,
            new MethodMeta()
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

class MyClient
{
    /**
     * This is an awesome function.
     *
     * @param RequestInterface & MyTypeNamespace\ParamType \$param
     * @return ResultInterface & MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function functionName(\Vendor\MyTypeNamespace\ParamType \$param) : \Vendor\MyTypeNamespace\ReturnType
    {
        \$response = (\$this->caller)('functionName', \$param);

        \Psl\Type\instance_of(\Vendor\MyTypeNamespace\ReturnType::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
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

use Phpro\SoapClient\Type\MultiArgumentRequest;
use Phpro\SoapClient\Type\ResultInterface;
use Vendor\MyTypeNamespace;
use Phpro\SoapClient\Exception\SoapException;

class MyClient
{
    /**
     * This is an awesome function.
     *
     * MultiArgumentRequest with following params:
     *
     * \Vendor\MyTypeNamespace\ParamType \$param
     * \Vendor\MyTypeNamespace\OtherParamType \$param2
     *
     * @param MultiArgumentRequest \$multiArgumentRequest
     * @return ResultInterface & MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function functionName(\Phpro\SoapClient\Type\MultiArgumentRequest \$multiArgumentRequest) : \Vendor\MyTypeNamespace\ReturnType
    {
        \$response = (\$this->caller)('functionName', \$multiArgumentRequest);

        \Psl\Type\instance_of(\Vendor\MyTypeNamespace\ReturnType::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
    }
}

CODE;
        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_can_deal_with_empty_params()
    {
        $assembler = new ClientMethodAssembler();
        $context = $this->createNoParamsContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\ResultInterface;
use Vendor\MyTypeNamespace;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\MultiArgumentRequest;

class MyClient
{
    /**
     * @return ResultInterface & MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function functionName() : \Vendor\MyTypeNamespace\ReturnType
    {
        \$response = (\$this->caller)('functionName', new MultiArgumentRequest([]));

        \Psl\Type\instance_of(\Vendor\MyTypeNamespace\ReturnType::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
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
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'Function_name',
            [
                new Parameter('param', 'param_type', $typeNamespace, new TypeMeta()),
            ],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('return_type')),
            $typeNamespace,
            new MethodMeta()
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

class MyClient
{
    /**
     * @param RequestInterface & MyTypeNamespace\ParamType \$param
     * @return ResultInterface & MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function function_name(\Vendor\MyTypeNamespace\ParamType \$param) : \Vendor\MyTypeNamespace\ReturnType
    {
        \$response = (\$this->caller)('Function_name', \$param);

        \Psl\Type\instance_of(\Vendor\MyTypeNamespace\ReturnType::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
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

    /**
     * @test
     */
    function it_deals_with_scalar_types_as_a_multi_arguments_request() {
        $assembler = new ClientMethodAssembler();
        $class = new ClassGenerator();
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'Function_name',
            [
                new Parameter('param', 'string', $typeNamespace, (new TypeMeta())->withIsSimple(true)),
            ],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('ReturnType')),
            $typeNamespace,
            new MethodMeta()
        );

        $context = new ClientMethodContext($class, $method);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\MultiArgumentRequest;
use Phpro\SoapClient\Type\ResultInterface;
use Vendor\MyTypeNamespace;
use Phpro\SoapClient\Exception\SoapException;

class MyClient
{
    /**
     * MultiArgumentRequest with following params:
     *
     * string \$param
     *
     * @param MultiArgumentRequest \$multiArgumentRequest
     * @return ResultInterface & MyTypeNamespace\ReturnType
     * @throws SoapException
     */
    public function function_name(\Phpro\SoapClient\Type\MultiArgumentRequest \$multiArgumentRequest) : \Vendor\MyTypeNamespace\ReturnType
    {
        \$response = (\$this->caller)('Function_name', \$multiArgumentRequest);

        \Psl\Type\instance_of(\Vendor\MyTypeNamespace\ReturnType::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_can_deal_with_scalar_return_types_on_single_arguments()
    {
        $assembler = new ClientMethodAssembler();
        $class = new ClassGenerator();
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('string')->withMeta(
                fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
            )),
            $typeNamespace,
            new MethodMeta()
        );

        $context = new ClientMethodContext($class, $method);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Type\MultiArgumentRequest;

class MyClient
{
    /**
     * @return ResultInterface & MixedResult<string>
     * @throws SoapException
     */
    public function functionName() : \Phpro\SoapClient\Type\MixedResult
    {
        \$response = (\$this->caller)('functionName', new MultiArgumentRequest([]));

        \Psl\Type\instance_of(\Phpro\SoapClient\Type\MixedResult::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_can_deal_with_scalar_return_types_on_multi_arguments()
    {
        $assembler = new ClientMethodAssembler();
        $class = new ClassGenerator();
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';
        $method = new ClientMethod(
            'functionName',
            [
                new Parameter('param1', 'string', $typeNamespace, (new TypeMeta())),
                new Parameter('param2', 'string', $typeNamespace, (new TypeMeta())),
            ],
            ReturnType::fromMetaData($typeNamespace, XsdType::create('string')->withMeta(
                fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
            )),
            $typeNamespace,
            new MethodMeta()
        );

        $context = new ClientMethodContext($class, $method);
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Type\MultiArgumentRequest;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Exception\SoapException;

class MyClient
{
    /**
     * MultiArgumentRequest with following params:
     *
     * string \$param1
     * string \$param2
     *
     * @param MultiArgumentRequest \$multiArgumentRequest
     * @return ResultInterface & MixedResult<string>
     * @throws SoapException
     */
    public function functionName(\Phpro\SoapClient\Type\MultiArgumentRequest \$multiArgumentRequest) : \Phpro\SoapClient\Type\MixedResult
    {
        \$response = (\$this->caller)('functionName', \$multiArgumentRequest);

        \Psl\Type\instance_of(\Phpro\SoapClient\Type\MixedResult::class)->assert(\$response);
        \Psl\Type\instance_of(\Phpro\SoapClient\Type\ResultInterface::class)->assert(\$response);

        return \$response;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }
}
