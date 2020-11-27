<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Exception\UnexpectedConfigurationException;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptionsResolverFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;
use Phpro\SoapClient\Soap\TypeConverter;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtSoapOptionsTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->wsdl = FIXTURE_DIR.'/wsdl/functional/string.wsdl';
        $this->resolver = ExtSoapOptionsResolverFactory::createForWsdl($this->wsdl);
    }

    /** @test */
    function it_is_possible_to_construct_from_empty_state()
    {
        $options = new ExtSoapOptions($this->wsdl, $expectedOptions = ['trace' => true]);
        $this->assertSame($this->wsdl, $options->getWsdl());
        $this->assertSame($expectedOptions, $options->getOptions());
    }

    /** @test */
    function it_contains_a_wsdl()
    {
        $wsdl = ExtSoapOptions::defaults($this->wsdl)->getWsdl();
        $this->assertSame($wsdl, $this->wsdl);
    }

    /** @test */
    function it_can_resolve_defaults()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl, [])->getOptions()
        );

        $this->assertTrue($options['trace']);
        $this->assertTrue($options['exceptions']);
        $this->assertSame(WSDL_CACHE_DISK, $options['cache_wsdl']);
        $this->assertSame(SOAP_SINGLE_ELEMENT_ARRAYS, $options['features']);
        $this->assertIsArray($options['typemap']);
    }

    /** @test */
    function it_is_possible_to_overwrite_defaults()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl, [
                'trace' => false,
                'proxy_host' => $proxyHost = 'http://localhost',
            ])->getOptions()
        );

        $this->assertFalse($options['trace']);
        $this->assertTrue($options['exceptions']);
        $this->assertSame($proxyHost, $options['proxy_host']);
    }

    /** @test */
    function it_is_possible_to_attach_a_wsdl_provider()
    {
        $wsdlProvider = $this->prophesize(WsdlProviderInterface::class);
        $wsdlProvider->provide($this->wsdl)->willReturn($newWsdl = 'new.wsdl');
        $options = ExtSoapOptions::defaults($this->wsdl, [])
            ->withWsdlProvider($wsdlProvider->reveal());

        $this->assertSame($newWsdl, $options->getWsdl());
    }

    /** @test */
    function it_is_possible_to_disable_wsdl_cache()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl)->disableWsdlCache()->getOptions()
        );

        $this->assertSame(WSDL_CACHE_NONE, $options['cache_wsdl']);
    }

    /** @test */
    function it_contains_a_default_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl);

        $typeMap = $options->getTypeMap();
        $this->assertInstanceOf(TypeConverter\TypeConverterCollection::class, $typeMap);
        $this->assertCount(4, $typeMap->getIterator());

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['typemap']);
        $this->assertCount(4, $resolved['typemap']);
        $this->assertSame('dateTime', $resolved['typemap'][0]['type_name']);
        $this->assertSame('date', $resolved['typemap'][1]['type_name']);
        $this->assertSame('decimal', $resolved['typemap'][2]['type_name']);
        $this->assertSame('double', $resolved['typemap'][3]['type_name']);
    }

    /** @test */
    function it_is_possible_to_replace_the_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl)
            ->withTypeMap($typeMap = new TypeConverter\TypeConverterCollection());

        $this->assertSame($typeMap, $options->getTypeMap());

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['typemap']);
        $this->assertCount(0, $resolved['typemap']);
    }

    /** @test */
    function it_is_possible_to_use_regular_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl, [
            'typemap' => [
                [
                    'type_name' => $typeName = 'hello',
                    'type_ns' => $typeNs = 'http://my-ns/xsd',
                    'from_xml' => function ($input) {
                        return $input;
                    },
                    'to_xml' => function ($input) {
                        return '<xml>'.$input.'</xml>';
                    },
                ]
            ]
        ]);

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['typemap']);
        $this->assertCount(1, $resolved['typemap']);
        $this->assertSame($typeName, $resolved['typemap'][0]['type_name']);
        $this->assertSame($typeNs, $resolved['typemap'][0]['type_ns']);

        $this->expectException(UnexpectedConfigurationException::class);
        $options->getTypeMap();
    }

    /** @test */
    function it_can_dynamically_add_a_default_clasmap()
    {
        $options = ExtSoapOptions::defaults($this->wsdl);

        $classMap = $options->getClassMap();
        $this->assertInstanceOf(ClassMapCollection::class, $classMap);
        $this->assertCount(0, $classMap->getIterator());

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['classmap']);
        $this->assertCount(0, $resolved['classmap']);
    }

    /** @test */
    function it_is_possible_to_replace_the_class_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl)
             ->withClassMap($classMap = new ClassMapCollection([
                 new ClassMap('wsdlType', 'PhpClass'),
             ]));

        $this->assertSame($classMap, $options->getClassMap());

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['classmap']);
        $this->assertCount(1, $resolved['classmap']);
        $this->assertSame('PhpClass', $resolved['classmap']['wsdlType']);
    }

    /** @test */
    function it_is_possible_to_use_regular_class_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl, [
            'classmap' => [
                'wsdlType' => 'PhpClass',
            ]
        ]);

        $resolved = $this->resolver->resolve($options->getOptions());
        $this->assertIsArray($resolved['classmap']);
        $this->assertCount(1, $resolved['classmap']);
        $this->assertSame('PhpClass', $resolved['classmap']['wsdlType']);

        $this->expectException(UnexpectedConfigurationException::class);
        $options->getClassMap();
    }

    /** @test */
    function it_can_accept_all_knwon_options()
    {
        $options = $this->resolver->resolve(
            (new ExtSoapOptions(
                $this->wsdl,
                $expectedOptions = [
                    'uri' => 'http://localhost',
                    'location' => 'http://localhost',
                    'soap_version' => SOAP_1_1,
                    'login' => 'user',
                    'password' => 'password',
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'proxy_host' => 'http://proxy',
                    'proxy_port' => '8888',
                    'proxy_login' => 'proxyuser',
                    'proxy_password' => 'proxypass',
                    'local_cert' => 'somecert.key',
                    'passphrase' => 'sslpass',
                    'compression' => SOAP_COMPRESSION_GZIP,
                    'encoding' => 'utf-8',
                    'trace' => true,
                    'classmap' => [],
                    'exceptions' => true,
                    'connection_timeout' => 900,
                    'default_socket_timeout' => 900,
                    'typemap' => [],
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'user_agent' => 'My Super SoapClient',
                    'stream_context' => stream_context_create(),
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'keep_alive' => false,
                    'ssl_method' => SOAP_SSL_METHOD_SSLv23,
                ]
            ))->getOptions()
        );

        foreach ($options as $key => $option) {
            $this->assertSame($expectedOptions[$key], $option);
        }
    }

    /** @test */
    public function it_contains_empty_metadata_options(): void
    {
        $options = new ExtSoapOptions($this->wsdl, []);
        $metadataOptions = $options->getMetadataOptions();

        self::assertEquals(MetadataOptions::empty(), $metadataOptions);
    }

    /** @test */
    public function it_adds_prefered_default_metadata_options(): void
    {
        $options = ExtSoapOptions::defaults($this->wsdl);
        $metadataOptions = $options->getMetadataOptions();
        $expectedMetadataOptions = MetadataOptions::empty()->withTypesManipulator(
            new TypesManipulatorChain(new IntersectDuplicateTypesStrategy())
        );

        self::assertEquals($expectedMetadataOptions, $metadataOptions);
    }

    /** @test */
    public function it_is_possible_to_change_metadata_options(): void
    {
        $options = ExtSoapOptions::defaults($this->wsdl);
        $metadataOptions = $options->getMetadataOptions();
        $expectedOptions = MetadataOptions::empty();

        $options->withMetadataOptions(function ($options) use ($metadataOptions, $expectedOptions) {
            self::assertSame($metadataOptions, $options);
            return $expectedOptions;
        });

        self::assertEquals($expectedOptions, $options->getMetadataOptions());
    }
}
