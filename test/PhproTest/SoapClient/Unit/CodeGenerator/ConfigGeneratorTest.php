<?php

use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\FileGenerator;

class ConfigGeneratorTest extends TestCase
{
    private $expected;

    public function setUp()
    {
        $this->expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;

return Config::create()
    ->setWsdl('wsdl.xml')
    ->setTypeDestination('src/type')
    ->setTypeNamespace('App\\\\Type')
    ->setClientDestination('src/client')
    ->setClientName('Client')
    ->setClientNamespace('App\\\\Client')
    ->setClassmapDestination('src/classmap')
    ->setClassmapName('Classmap')
    ->setClassmapNamespace('App\\\\Classmap')
    ->addRule(
        new Rules\TypenameMatchesRule(
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler()),
            ]),
            '/Request$/i'
        )
    )
    ->addRule(
        new Rules\TypenameMatchesRule(
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
                new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())),
            ]),
            '/Response$/i'
        )
    );

CONTENT;
    }

    public function testGenerate()
    {
        $context = new ConfigContext();
        $context
            ->addSetter('setWsdl', 'wsdl.xml')
            ->addSetter('setTypeDestination', 'src/type')
            ->addSetter('setTypeNamespace', 'App\\\\Type')
            ->addSetter('setClientDestination', 'src/client')
            ->addSetter('setClientName', 'Client')
            ->addSetter('setClientNamespace', 'App\\\\Client')
            ->addSetter('setClassmapDestination', 'src/classmap')
            ->addSetter('setClassmapName', 'Classmap')
            ->addSetter('setClassmapNamespace', 'App\\\\Classmap')
            ->setRequestRegex('/Request$/i')
            ->setResponseRegex('/Response$/i');

        $generator = new ConfigGenerator();
        $generated = $generator->generate(new FileGenerator(), $context);
        self::assertEquals($this->expected, $generated);
    }

    public function testGenerateWithoutRegex()
    {
        $context = new ConfigContext();
        $context
            ->addSetter('setWsdl', 'wsdl.xml')
            ->addSetter('setTypeDestination', 'src/type')
            ->addSetter('setTypeNamespace', 'App\\\\Type')
            ->addSetter('setClientDestination', 'src/client')
            ->addSetter('setClientName', 'Client')
            ->addSetter('setClientNamespace', 'App\\\\Client')
            ->addSetter('setClassmapDestination', 'src/classmap')
            ->addSetter('setClassmapName', 'Classmap')
            ->addSetter('setClassmapNamespace', 'App\\\\Classmap');

        $generator = new ConfigGenerator();
        $generated = $generator->generate(new FileGenerator(), $context);
        self::assertEquals($this->expected, $generated);
    }
}
