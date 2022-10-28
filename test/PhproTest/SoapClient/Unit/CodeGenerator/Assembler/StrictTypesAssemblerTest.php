<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\FileGenerator;
use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\StrictTypesAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\FileAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssembler;
use Phpro\SoapClient\CodeGenerator\Assembler\GetterAssemblerOptions;
use Phpro\SoapClient\CodeGenerator\Context\FileContext;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class FileAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class StrictTypesAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler(): void
    {
        $assembler = new StrictTypesAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_file_context(): void
    {
        $assembler = new StrictTypesAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    public function it_assembles_with_strict_types(): void
    {
        $assembler = new StrictTypesAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $class = new ClassGenerator();
        $class->setNamespaceName('TestNamespace');
        $class->setName('TestClass');

        $code = $context->getFileGenerator()->setClass($class)->generate();

        $expected = <<<CODE
<?php

declare(strict_types=1);

namespace TestNamespace;

class TestClass
{
}


CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @return FileContext
     */
    private function createContext(): FileContext
    {
        return new FileContext(new FileGenerator());
    }
}
