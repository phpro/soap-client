<?php

use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use PhproTest\SoapClient\Util\SyntaxChecker;
use PHPUnit\Framework\TestCase;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;
use Zend\Code\Generator\FileGenerator;

class TypeGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<BODY
<?php

namespace App\Type;

class MyType
{

    /**
     * @var string
     */
    private \$test = null;

    /**
     * @return string
     */
    public function getTest()
    {
        return \$this->test;
    }

    /**
     * @param string \$test
     */
    public function setTest(\$test)
    {
        \$this->test = \$test;
    }


}


BODY;
        $ruleSet = new RuleSet(
            [
                new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())),
                new Rules\AssembleRule(new Assembler\SetterAssembler(new Assembler\SetterAssemblerOptions())),
                new Rules\AssembleRule(new Assembler\PropertyAssembler()),
            ]
        );
        $generator = new \Phpro\SoapClient\CodeGenerator\TypeGenerator($ruleSet);
        $type = new Type('App\\Type', 'MyType', ['test' => 'string']);
        $generated = $generator->generate(new FileGenerator(), $type);
        self::assertEquals($expected, $generated);
        self::assertTrue(SyntaxChecker::isValidPHP($generated));
    }
}
