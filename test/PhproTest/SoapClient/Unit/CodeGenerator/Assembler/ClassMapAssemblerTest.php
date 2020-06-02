<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ClassMapAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Laminas\Code\Generator\FileGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class ClassMapAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ClassMapAssemblerTest extends TestCase
{
    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new ClassMapAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_classmap_context()
    {
        $assembler = new ClassMapAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_classmap()
    {
        $assembler = new ClassMapAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getFile()->generate();
        $expected = <<<CODE
<?php

namespace ClassMapNamespace;

use MyNamespace as Type;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;

class ClassMap
{

    public static function getCollection() : \Phpro\SoapClient\Soap\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection([
            new ClassMap('MyType', Type\MyType::class),
        ]);
    }


}


CODE;
        $this->assertEquals($expected, $code);
    }

    /**
     * @return ClassMapContext
     */
    private function createContext()
    {
        $file = new FileGenerator();
        $typeMap = new TypeMap($namespace = 'MyNamespace', [
            new Type(
                $namespace,
                'MyType',
                [
                    new Property('myProperty', 'string', $namespace)
                ]
            ),
        ]);

        return new ClassMapContext($file, $typeMap, 'ClassMap', 'ClassMapNamespace');
    }
}
