<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\ClassMapAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PhproTest\SoapClient\Unit\CodeGenerator\ConfigurationHelper;
use Laminas\Code\Generator\FileGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
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
    use ConfigurationHelper;

    #[Test]
    function it_is_an_assembler()
    {
        $assembler = new ClassMapAssembler();
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    #[Test]
    function it_can_assemble_classmap_context()
    {
        $assembler = new ClassMapAssembler();
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    #[Test]
    function it_assembles_a_classmap()
    {
        $assembler = new ClassMapAssembler();
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getFile()->generate();
        $expected = <<<CODE
<?php

namespace ClassMapNamespace;

use Soap\Encoding\ClassMap\ClassMapCollection;
use Soap\Encoding\ClassMap\ClassMap;

class MyClassMap
{
    public static function types(): \Soap\Encoding\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection(
            new ClassMap('http://my-namespace.com', 'MyType', \MyNamespace\MyType::class),
        );
    }

    public static function enums(): \Soap\Encoding\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection(
            new ClassMap('http://my-namespace.com', 'MyEnum', \MyNamespace\MyEnum::class),
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
        $namespaces = $this->createTypeNamespaceMap('MyNamespace');
        $typeMap = new TypeMap($namespaces, [
            new Type(
                $namespaces,
                'MyType',
                'MyType',
                [
                    Property::fromMetaData(
                        $namespaces,
                        new MetaProperty('myProperty', XsdType::guess('string'))
                    ),
                ],
                (new XsdType('MyType'))
                    ->withXmlNamespace('http://my-namespace.com')
            ),
            new Type(
                $namespaces,
                'MyEnum',
                'MyEnum',
                [],
                (new XsdType('MyEnum'))
                    ->withXmlNamespace('http://my-namespace.com')
                    ->withMeta(
                        static fn (TypeMeta $meta) => $meta
                            ->withIsSimple(true)
                            ->withEnums(['value1', 'value2'])
                    )
            ),
        ]);

        return new ClassMapContext($file, $typeMap, $this->createClassMapConfig('MyClassMap', 'ClassMapNamespace'));
    }
}
