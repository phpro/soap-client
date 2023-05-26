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
use Soap\Engine\Metadata\Model\Property as MetaProperty;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;

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
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;

class MyClassMap
{
    public static function getCollection() : \Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection(
            new ClassMap('MyType', Type\MyType::class),
        );
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
                    Property::fromMetaData(
                        $namespace,
                        new MetaProperty('myProperty', XsdType::guess('string'))
                    ),
                ],
                new TypeMeta(),
            ),
        ]);

        return new ClassMapContext($file, $typeMap, 'MyClassMap', 'ClassMapNamespace');
    }
}
