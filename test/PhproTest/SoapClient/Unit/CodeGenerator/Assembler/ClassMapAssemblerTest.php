<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\ClassMapAssembler;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClassMapAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class ClassMapAssemblerTest extends \PHPUnit_Framework_TestCase
{
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

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;

new ClassMapCollection([
    new ClassMap('MyType', \MyNamespace\MyType::class),
]);

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @return ClassMapContext
     */
    private function createContext()
    {
        $file = new FileGenerator();
        $typeMap = new TypeMap('MyNamespace', [
            'MyType' => [
                'myProperty' => 'string',
            ]
        ]);

        return new ClassMapContext($file, $typeMap);
    }
}
