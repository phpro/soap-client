<?php

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use PhproTest\SoapClient\Util\SyntaxChecker;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\FileGenerator;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;

class ClassmapGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<CONTENT
<?php

namespace App;

use App\Types as Type;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;

class MyClassmap
{

    public static function getCollection() : \Phpro\SoapClient\Soap\ClassMap\ClassMapCollection
    {
        return new ClassMapCollection([

        ]);
    }


}


CONTENT;
        $ruleSet = new RuleSet([new Rules\AssembleRule(new Assembler\ClassMapAssembler()),]);
        $generator = new ClassMapGenerator($ruleSet, 'MyClassmap', 'App');
        $generated = $generator->generate(new FileGenerator(), new TypeMap('App/Types', []));
        self::assertEquals($expected, $generated);
        self::assertTrue(SyntaxChecker::isValidPHP($generated));
    }
}
