<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap;

use Http\Discovery\Psr18ClientDiscovery;
use Phpro\SoapClient\Soap\Driver\CodeGeneratingDriver;
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\Engine\Engine;
use Soap\Engine\LazyEngine;
use Soap\Engine\SimpleEngine;
use Soap\Engine\Transport;
use Soap\Psr18Transport\Psr18Transport;
use Soap\Psr18Transport\Wsdl\Psr18Loader;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

final class CodeGeneratorEngineFactory
{
    /**
     * @param non-empty-string $wsdlLocation
     */
    public static function create(
        string $wsdlLocation,
        ?WsdlLoader $loader = null,
        ?Transport $transport = null,
        ?MetadataOptions $metadataOptions = null
    ): Engine {
        $loader ??= new FlatteningLoader(Psr18Loader::createForClient(Psr18ClientDiscovery::find()));
        $transport ??= Psr18Transport::createWithDefaultClient();
        $metadataOptions ??= MetadataOptions::empty()->withTypesManipulator(
            // Ext-soap is not able to work with duplicate types (see FAQ)
            // Therefore, we decided to combine all duplicate types into 1 big intersected type by default instead.
            // Therefore it will always be usable, but might contain some empty properties.
            // It has it's limitations but it is workable until ext-soap handles XSD namespaces properly.
            new IntersectDuplicateTypesStrategy()
        );

        return new LazyEngine(static function () use ($wsdlLocation, $loader, $transport, $metadataOptions) {
            $wsdl = (new Wsdl1Reader($loader))($wsdlLocation);
            $metadataProvider = new Wsdl1MetadataProvider(
                $wsdl,
                ServiceSelectionCriteria::defaults()->withAllowHttpPorts(false)
            );

            $driver = new CodeGeneratingDriver(
                MetadataFactory::manipulated($metadataProvider->getMetadata(), $metadataOptions)
            );

            return new SimpleEngine($driver, $transport);
        });
    }
}
