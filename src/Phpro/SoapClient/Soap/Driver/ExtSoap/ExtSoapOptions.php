<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
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

    public function __construct(string $wsdl, array $options = [])
    {
        $this->wsdl = $wsdl;
        $this->options = $options;
        $this->wsdlProvider = new MixedWsdlProvider();
    }

    public static function defaults(string $wsdl, array $options = []): self
    {
        return new self(
            $wsdl,
            array_merge(
                [
                    'trace' => true,
                    'exceptions' => true,
                    'keep_alive' => true,
                    'cache_wsdl' => WSDL_CACHE_BOTH,
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
        );
    }

    public function getWsdl(): string
    {
        return $this->wsdlProvider->provide($this->wsdl);
    }

    public function getOptions(): array
    {
        return ExtSoapOptionsResolverFactory::create()->resolve($this->options);
    }

    public function withWsdlProvider(WsdlProviderInterface $wsdlProvider): self
    {
        $this->wsdlProvider = $wsdlProvider;

        return $this;
    }

    public function getClassMap(): ClassMapCollection
    {
        $this->options['classmap'] = $this->options['classmap'] ?? new ClassMapCollection();

        return $this->options['classmap'];
    }

    public function withClassMap(ClassMapCollection $classMapCollection): self
    {
        $this->options['classmap'] = $classMapCollection;

        return $this;
    }

    public function getTypeMap(): TypeConverterCollection
    {
        $this->options['typemap'] = $this->options['typemap'] ?? new TypeConverterCollection();

        return $this->options['typemap'];
    }

    public function withTypeMap(TypeConverterCollection $typeConverterCollection): self
    {
        $this->options['typemap'] = $typeConverterCollection;

        return $this;
    }

    public function disableWsdlCache(): self
    {
        $this->options['wsdl_cache'] = WSDL_CACHE_NONE;

        return $this;
    }
}
