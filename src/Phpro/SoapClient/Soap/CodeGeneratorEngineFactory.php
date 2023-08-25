<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap;

use Http\Discovery\Psr18ClientDiscovery;
use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\Engine\Engine;
use Soap\Engine\LazyEngine;
use Soap\Engine\NoopTransport;
use Soap\Engine\PartialDriver;
use Soap\Engine\SimpleEngine;
use Soap\Psr18Transport\Wsdl\Psr18Loader;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Parser\Context\ParserContext;
use Soap\WsdlReader\Wsdl1Reader;

final class CodeGeneratorEngineFactory
{
    /**
     * @param non-empty-string $wsdlLocation
     */
    public static function create(
        string $wsdlLocation,
        ?WsdlLoader $loader = null,
        ?MetadataOptions $metadataOptions = null,
        ?SoapVersion $preferredSoapVersion = null,
        ?ParserContext $parserContext = null,
    ): Engine {
        $loader ??= new FlatteningLoader(Psr18Loader::createForClient(Psr18ClientDiscovery::find()));
        $metadataOptions ??= MetadataOptions::empty()->withTypesManipulator(
            // Ext-soap is not able to work with duplicate types (see FAQ)
            // Therefore, we decided to combine all duplicate types into 1 big intersected type by default instead.
            // Therefore it will always be usable, but might contain some empty properties.
            // It has it's limitations but it is workable until ext-soap handles XSD namespaces properly.
            new IntersectDuplicateTypesStrategy()
        );

        return new LazyEngine(static function () use (
            $wsdlLocation,
            $loader,
            $metadataOptions,
            $parserContext,
            $preferredSoapVersion
        ) {
            $wsdl = (new Wsdl1Reader($loader))($wsdlLocation, $parserContext);
            $metadataProvider = new Wsdl1MetadataProvider(
                $wsdl,
                ServiceSelectionCriteria::defaults()
                    ->withAllowHttpPorts(false)
                    ->withPreferredSoapVersion($preferredSoapVersion)
            );

            return new SimpleEngine(
                new PartialDriver(
                    metadata: MetadataFactory::manipulated($metadataProvider->getMetadata(), $metadataOptions),
                ),
                new NoopTransport()
            );
        });
    }
}
