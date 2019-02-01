<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Engine\Metadata\MetadataProviderInterface;
use PhproTest\SoapClient\Integration\Soap\Engine\AbstractMetadataProviderTest;

class ExtSoapMetadataProviderTest extends AbstractMetadataProviderTest
{
    /**
     * @var MetadataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var AbusedClient
     */
    protected $client;

    protected function getMetadataProvider(): MetadataProviderInterface
    {
        return $this->metadataProvider;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->metadataProvider = ExtSoapDriver::createFromClient(
            $this->client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl)
                    ->disableWsdlCache()
            )
        );
    }
}
