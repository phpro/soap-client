<?php

use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\FileGenerator;

class ConfigGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Engine\Engine;

return Config::create()
    ->setEngine(new Engine(
        \$driver = ExtSoapDriver::createFromOptions(ExtSoapOptions::defaults('wsdl.xml', []));
        \$handler = new ExtSoapClientHandle(\$driver->getClient());
    ))
    ->setTypeDestination('src/type')
    ->setTypeNamespace('App\\\\Type')
    ->setClientDestination('src/client')
    ->setClientName('Client')
    ->setClientNamespace('App\\\\Client')
    ->setClassmapDestination('src/classmap')
    ->setClassmapName('Classmap')
    ->setClassmapNamespace('App\\\\Classmap')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler()))
    ->addRule(
        new Rules\TypenameMatchesRule(
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
            ]),
            '/Request$/i'
        )
    )
    ->addRule(
        new Rules\TypenameMatchesRule(
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
            ]),
            '/Response$/i'
        )
    )
;

CONTENT;
        $context = new ConfigContext();
        $context
            ->setWsdl('wsdl.xml')
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
        self::assertEquals($expected, $generated);
    }

    public function testGenerateWithoutRegex()
    {
        $expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\Soap\Engine\Engine;

return Config::create()
    ->setEngine(new Engine(
        \$driver = ExtSoapDriver::createFromOptions(ExtSoapOptions::defaults('wsdl.xml', []));
        \$handler = new ExtSoapClientHandle(\$driver->getClient());
    ))
    ->setTypeDestination('src/type')
    ->setTypeNamespace('App\\\\Type')
    ->setClientDestination('src/client')
    ->setClientName('Client')
    ->setClientNamespace('App\\\\Client')
    ->setClassmapDestination('src/classmap')
    ->setClassmapName('Classmap')
    ->setClassmapNamespace('App\\\\Classmap')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler()))
;

CONTENT;
        $context = new ConfigContext();
        $context
            ->setWsdl('wsdl.xml')
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
        self::assertEquals($expected, $generated);
    }

}
