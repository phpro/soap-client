<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap;

use Phpro\SoapClient\Soap\ExtSoap\Metadata\Manipulators\DuplicateTypes\IntersectDuplicateTypesStrategy;
use Phpro\SoapClient\Soap\Metadata\MetadataFactory;
use Phpro\SoapClient\Soap\Metadata\MetadataOptions;
use Soap\Engine\Engine;
use Soap\Engine\LazyEngine;
use Soap\Engine\SimpleEngine;
use Soap\Engine\Transport;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapMetadata;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\Psr18Transport\Psr18Transport;

final class DefaultEngineFactory
{
    public static function create(
        ExtSoapOptions $options,
        ?Transport $transport = null,
        ?MetadataOptions $metadataOptions = null
    ): Engine {
        $transport ??= Psr18Transport::createWithDefaultClient();
        $metadataOptions ??= MetadataOptions::empty()->withTypesManipulator(
            // Ext-soap is not able to work with duplicate types (see FAQ)
            // Therefore, we decided to combine all duplicate types into 1 big intersected type by default instead.
            // Therefore it will always be usable, but might contain some empty properties.
            // It has it's limitations but it is workable until ext-soap handles XSD namespaces properly.
            new IntersectDuplicateTypesStrategy()
        );

        return new LazyEngine(static function () use ($options, $transport, $metadataOptions) {
            $client = AbusedClient::createFromOptions($options);
            $driver = ExtSoapDriver::createFromClient(
                $client,
                MetadataFactory::manipulated(
                    new ExtSoapMetadata($client),
                    $metadataOptions
                )
            );

            return new SimpleEngine($driver, $transport);
        });
    }
}
