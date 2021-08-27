<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Assembler\ClientConstructorAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\Exception\AssemblerException;
use PHPUnit\Framework\TestCase;

class ClientConstructorAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ClientConstructorAssembler();
        $this->assertInstanceOf(ClientConstructorAssembler::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_client_method_context()
    {
        $assembler = new ClientConstructorAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    private function createContext(): ClientContext
    {
        $class = new ClassGenerator();
        $class->setName('Vendor\\MyNamespace\\MyClient');
        $typeNamespace = 'Vendor\\MyTypeNamespace';

        return new ClientContext(
            $class,
            'MyClient',
            $typeNamespace
        );
    }

    /**
     * @test
     */
    function it_assembles_a_client_constructor()
    {
        $assembler = new ClientConstructorAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

use Phpro\SoapClient\Caller\Caller;

class MyClient
{
    /**
     * @var Caller
     */
    private \$caller;

    public function __construct(\Phpro\SoapClient\Caller\Caller \$caller)
    {
        \$this->caller = \$caller;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_throws_an_exception_when_wrong_context_is_passed() {
        $clientMethodAssembler = new ClientConstructorAssembler();
        $context = $this->createMock(ContextInterface::class);
        $this->expectException(AssemblerException::class);
        $this->expectExceptionMessage(sprintf(
                'Phpro\SoapClient\CodeGenerator\Assembler\ClientConstructorAssembler::assemble '.
                'expects an Phpro\SoapClient\CodeGenerator\Context\ClientContext as input %s given',
                get_class($context)
            )
        );
        $clientMethodAssembler->assemble($context);
    }
}
