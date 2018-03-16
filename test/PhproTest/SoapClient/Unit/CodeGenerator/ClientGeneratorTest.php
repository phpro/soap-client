<?php

use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethod;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\CodeGenerator\Model\Parameter;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSet;
use PhproTest\SoapClient\Util\SyntaxChecker;
use PHPUnit\Framework\TestCase;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Assembler;
use Zend\Code\Generator\FileGenerator;

class ClientGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<BODY
<?php

namespace App;

class MyClient extends \Phpro\SoapClient\Client
{

    public function myClientMethod(string \$test) : \Mynamespace\MyReturnType
    {
        return \$this->call('test', \$test);
    }


}


BODY;
        $ruleSet = new RuleSet([new Rules\AssembleRule(new Assembler\ClientMethodAssembler())]);
        $generator = new ClientGenerator($ruleSet);
        $clientMethodMap = new ClientMethodMap(
            [new ClientMethod('myClientMethod', [new Parameter('test', 'string')], 'MyReturnType', 'Mynamespace')]
        );
        $client = new Client('MyClient', 'App', $clientMethodMap);
        $generated = $generator->generate(new FileGenerator(), $client);
        self::assertEquals($expected, $generated);
        self::assertTrue(SyntaxChecker::isValidPHP($generated));
    }
}
