<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Exception\UnexpectedConfigurationException;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Engine\Metadata\Manipulators\TypesManipulatorChain;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataOptions;
use Phpro\SoapClient\Soap\TypeConverter;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;
use Phpro\SoapClient\Wsdl\Provider\MixedWsdlProvider;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;

class ExtSoapOptions
{
    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $options;

    /**
     * @var WsdlProviderInterface
     */
    private $wsdlProvider;

    /**
     * @var MetadataOptions
     */
    private $metadataOptions;

    public function __construct(string $wsdl, array $options = [])
    {
        $this->wsdl = $wsdl;
        $this->options = $options;
        $this->wsdlProvider = new MixedWsdlProvider();
        $this->metadataOptions = MetadataOptions::empty();
    }

    public static function defaults(string $wsdl, array $options = []): self
    {
        return (
            new self(
                $wsdl,
                array_merge(
                    [
                        'trace' => true,
                        'exceptions' => true,
                        'keep_alive' => true,
                        'cache_wsdl' => WSDL_CACHE_DISK, // Avoid memory cache: this causes SegFaults from time to time.
                        'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                        'typemap' => new TypeConverterCollection([
                            new TypeConverter\DateTimeTypeConverter(),
                            new TypeConverter\DateTypeConverter(),
                            new TypeConverter\DecimalTypeConverter(),
                            new TypeConverter\DoubleTypeConverter()
                        ]),
                    ],
                    $options
                )
            )
        )->withMetadataOptions(static function (MetadataOptions $options): MetadataOptions {
            // Ext-soap is not able to work with duplicate types (see FAQ)
            // Therefore, we decided to combine all duplicate types into 1 big intersected type instead.
            // Therefore it will always be usable, but might contain some empty properties.
            // It has it's limitations but it is workable until ext-soap handles XSD namespaces properly.
            return $options->withTypesManipulator(new TypesManipulatorChain(
                new IntersectDuplicateTypesStrategy()
            ));
        });
    }

    public function getWsdl(): string
    {
        return $this->wsdlProvider->provide($this->wsdl);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withWsdlProvider(WsdlProviderInterface $wsdlProvider): self
    {
        $this->wsdlProvider = $wsdlProvider;

        return $this;
    }

    public function getClassMap(): ClassMapCollection
    {
        return $this->fetchOptionOfTypeWithDefault(
            'classmap',
            ClassMapCollection::class,
            new ClassMapCollection()
        );
    }

    public function withClassMap(ClassMapCollection $classMapCollection): self
    {
        $this->options['classmap'] = $classMapCollection;

        return $this;
    }

    public function getTypeMap(): TypeConverterCollection
    {
        return $this->fetchOptionOfTypeWithDefault(
            'typemap',
            TypeConverterCollection::class,
            new TypeConverterCollection()
        );
    }

    public function withTypeMap(TypeConverterCollection $typeConverterCollection): self
    {
        $this->options['typemap'] = $typeConverterCollection;

        return $this;
    }

    public function disableWsdlCache(): self
    {
        $this->options['cache_wsdl'] = WSDL_CACHE_NONE;

        return $this;
    }

    public function withMetadataOptions(callable $manipulator): self
    {
        $this->metadataOptions = $manipulator($this->metadataOptions);

        return $this;
    }

    public function getMetadataOptions(): MetadataOptions
    {
        return $this->metadataOptions;
    }

    private function fetchOptionOfTypeWithDefault(string $key, string $type, $default)
    {
        $this->options[$key] = $this->options[$key] ?? $default;

        if (!$this->options[$key] instanceof $type) {
            throw UnexpectedConfigurationException::expectedTypeButGot($key, $type, $this->options[$key]);
        }

        return $this->options[$key];
    }
}
