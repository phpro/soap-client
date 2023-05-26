<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\ConfigGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\FileGenerator;

class ConfigGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Soap\CodeGeneratorEngineFactory;

return Config::create()
    ->setEngine(\$engine = CodeGeneratorEngineFactory::create(
        'wsdl.xml'
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
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
        new Assembler\ImmutableSetterAssemblerOptions()
    )))
    ->addRule(
        new Rules\IsRequestRule(
            \$engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
            ])
        )
    )
    ->addRule(
        new Rules\IsResultRule(
            \$engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
            ])
        )
    )
    ->addRule(
        new Rules\IsExtendingTypeRule(
            \$engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\ExtendingTypeAssembler())
        )
    )
    ->addRule(
        new Rules\IsAbstractTypeRule(
            \$engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\AbstractClassAssembler())
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
            ->addSetter('setClassmapNamespace', 'App\\\\Classmap');

        $generator = new ConfigGenerator();
        $generated = $generator->generate(new FileGenerator(), $context);
        self::assertEquals($expected, $generated);
    }

    public function testGenerateWithoutDocblocks(): void
    {
        $expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Soap\CodeGeneratorEngineFactory;

return Config::create()
    ->setEngine(\$engine = CodeGeneratorEngineFactory::create(
        'wsdl.xml'
    ))
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(
        (new Assembler\GetterAssemblerOptions())->withDocBlocks(false)
    )))
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
        (new Assembler\ImmutableSetterAssemblerOptions())->withDocBlocks(false)
    )))
    ->addRule(
        new Rules\IsRequestRule(
            \$engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler(
                    (new Assembler\ConstructorAssemblerOptions())->withDocBlocks(false)
                )),
            ])
        )
    )
    ->addRule(
        new Rules\IsResultRule(
            \$engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
            ])
        )
    )
    ->addRule(
        new Rules\IsExtendingTypeRule(
            \$engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\ExtendingTypeAssembler())
        )
    )
    ->addRule(
        new Rules\IsAbstractTypeRule(
            \$engine->getMetadata(),
            new Rules\AssembleRule(new Assembler\AbstractClassAssembler())
        )
    )
;

CONTENT;
        $context = new ConfigContext();
        $context
            ->setWsdl('wsdl.xml')
            ->setGenerateDocblocks(false);

        $generator = new ConfigGenerator();
        $generated = $generator->generate(new FileGenerator(), $context);
        self::assertEquals($expected, $generated);
    }
}
