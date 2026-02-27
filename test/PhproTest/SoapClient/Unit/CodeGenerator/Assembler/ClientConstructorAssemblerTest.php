<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\Assembler\ClientConstructorAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\Exception\AssemblerException;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClientConstructorAssemblerTest extends TestCase
{
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new ClientConstructorAssembler();
        $this->assertInstanceOf(ClientConstructorAssembler::class, $assembler);
    }

    #[Test]
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

        return new ClientContext(
            $class,
            $this->createClientConfig('MyClient', 'Vendor\\MyNamespace')
        );
    }

    #[Test]
    function it_assembles_a_client_constructor()
    {
        $assembler = new ClientConstructorAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace Vendor\MyNamespace;

class MyClient
{
    private \Phpro\SoapClient\Caller\Caller \$caller;

    public function __construct(\Phpro\SoapClient\Caller\Caller \$caller)
    {
        \$this->caller = \$caller;
    }
}

CODE;

        $this->assertEquals($expected, $code);
    }

    #[Test]
    function it_throws_an_exception_when_wrong_context_is_passed() {
        $clientMethodAssembler = new ClientConstructorAssembler();
        $context = $this->createStub(ContextInterface::class);
        $this->expectException(AssemblerException::class);
        $this->expectExceptionMessage(sprintf(
                'Phpro\SoapClient\CodeGenerator\Assembler\ClientConstructorAssembler::assemble '.
                'expects an Phpro\SoapClient\CodeGenerator\Context\ClientContext as input %s given',
                $context::class
            )
        );
        $clientMethodAssembler->assemble($context);
    }
}
