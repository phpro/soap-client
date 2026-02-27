<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Phpro\SoapClient\CodeGenerator\CodingStandards\DefaultCodingStandardsStrategy;
use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\FileGenerator;

class ClientFactoryGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<BODY
<?php

namespace App\Client;

use App\Client\Myclient;
use App\Classmap\SomeClassmap;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Soap\EngineOptions;
use Phpro\SoapClient\Caller\EventDispatchingCaller;
use Phpro\SoapClient\Caller\EngineCaller;
use Soap\Encoding\EncoderRegistry;

class MyclientFactory
{
    /**
     * This factory can be used as a starting point to create your own specialized
     * factory. Feel free to modify.
     *
     * @param non-empty-string \$wsdl
     */
    public static function factory(string \$wsdl): \App\Client\Myclient
    {
        \$engine = DefaultEngineFactory::create(
            EngineOptions::defaults(\$wsdl)
                ->withEncoderRegistry(
                    EncoderRegistry::default()
                        ->addClassMapCollection(SomeClassmap::types())
                        ->addBackedEnumClassMapCollection(SomeClassmap::enums())
                )
                // If you want to enable WSDL caching:
                // ->withCache() 
                // If you want to use Alternate HTTP settings:
                // ->withWsdlLoader()
                // ->withTransport()
                // If you want specific SOAP setting:
                // ->withWsdlParserContext()
                // ->withWsdlServiceSelectionCriteria()
        );

        \$eventDispatcher = new EventDispatcher();
        \$caller = new EventDispatchingCaller(new EngineCaller(\$engine), \$eventDispatcher);

        return new Myclient(\$caller);
    }
}


BODY;
        $clientConfig = new ClientConfig('Myclient', new Destination('/app/client', 'App\\Client'));
        $clientContext = new ClientContext(new ClassGenerator(), $clientConfig);

        $typeNamespaceMap = TypeNamespaceMap::create(new Destination('/app/types', 'App\\Types'));
        $codeGeneratorContext = new CodeGeneratorContext($typeNamespaceMap, new DefaultCodingStandardsStrategy());
        $classMapConfig = new ClassMapConfig('SomeClassmap', new Destination('/app/classmap', 'App\\Classmap'));
        $classMapContext = new ClassMapContext(
            new FileGenerator(),
            new TypeMap($codeGeneratorContext, []),
            $classMapConfig,
            $codeGeneratorContext
        );
        $context = new ClientFactoryContext($clientContext, $classMapContext);
        $generator = new ClientFactoryGenerator();
        self::assertEquals($expected, $generator->generate(new FileGenerator(), $context));
    }
}
