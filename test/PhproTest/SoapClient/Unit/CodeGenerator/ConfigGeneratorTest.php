<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
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
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;

return Config::create()
    ->setEngine(\$engine = DefaultEngineFactory::create(
        EngineOptions::defaults('wsdl.xml')
    ))
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('src/type', 'App\\\\Type'))
        // ->withStrategy(new PrefixBasedTypeNamespaceStrategy())
    )
    ->setClient(new ClientConfig('Client', new Destination('src/client', 'App\\\\Client')))
    ->setClassMap(new ClassMapConfig('Classmap', new Destination('src/classmap', 'App\\\\Classmap')))
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
        $context->setWsdl('wsdl.xml');

        $typeDestination = new Destination('src/type', 'App\\Type');
        $context->setTypeDestination($typeDestination);

        $clientConfig = new ClientConfig('Client', new Destination('src/client', 'App\\Client'));
        $context->setClientConfig($clientConfig);

        $classMapConfig = new ClassMapConfig('Classmap', new Destination('src/classmap', 'App\\Classmap'));
        $context->setClassMapConfig($classMapConfig);

        $generator = new ConfigGenerator();
        $generated = $generator->generate(new FileGenerator(), $context);
        self::assertEquals($expected, $generated);
    }

    public function testGenerateWithDetectedXmlNamespaces(): void
    {
        $expected = <<<CONTENT
<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;

return Config::create()
    ->setEngine(\$engine = DefaultEngineFactory::create(
        EngineOptions::defaults('wsdl.xml')
    ))
    ->setTypeNamespaceMap(
        TypeNamespaceMap::create(new Destination('src/type', 'App\\\\Type'))
        // ->withMapping('http://www.opengis.net/gml/3.2', new Destination('src/type/Gml', 'App\\\\Type\\\\Gml'))
        // ->withMapping('http://xoev.de/schemata/xzufi/2_2_0', new Destination('src/type/Xzufi', 'App\\\\Type\\\\Xzufi'))
        // ->withStrategy(new PrefixBasedTypeNamespaceStrategy())
    )
    ->setClient(new ClientConfig('Client', new Destination('src/client', 'App\\\\Client')))
    ->setClassMap(new ClassMapConfig('Classmap', new Destination('src/classmap', 'App\\\\Classmap')))
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
        $context->setWsdl('wsdl.xml');

        $typeDestination = new Destination('src/type', 'App\\Type');
        $context->setTypeDestination($typeDestination);
        $context->setDetectedXmlNamespaces([
            'http://www.opengis.net/gml/3.2' => 'gml',
            'http://xoev.de/schemata/xzufi/2_2_0' => 'xzufi',
        ]);

        $clientConfig = new ClientConfig('Client', new Destination('src/client', 'App\\Client'));
        $context->setClientConfig($clientConfig);

        $classMapConfig = new ClassMapConfig('Classmap', new Destination('src/classmap', 'App\\Classmap'));
        $context->setClassMapConfig($classMapConfig);

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
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;

return Config::create()
    ->setEngine(\$engine = DefaultEngineFactory::create(
        EngineOptions::defaults('wsdl.xml')
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
