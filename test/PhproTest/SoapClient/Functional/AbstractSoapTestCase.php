<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional;

use Phpro\SoapClient\Soap\Handler\LocalSoapServerHandle;
use Phpro\SoapClient\Soap\SoapClient as PhproSoapClient;
use Phpro\SoapClient\Wsdl\Provider\InMemoryWsdlProvider;
use PHPUnit\Framework\TestCase;
use SoapServer;

abstract class AbstractSoapTestCase extends TestCase
{
    /**
     * @var SoapServer
     */
    protected $server;

    /**
     * @var PhproSoapClient
     */
    protected $client;

    /**
     * @return SoapServer
     */
    abstract protected function configureServer(SoapServer $server);

    /**
     * @return string|null
     */
    abstract protected function getWsdl();
    abstract protected function getSoapOptions(): array;

    protected function setUp() {
        $wsdl = $this->getWsdl();
        $options = $this->getSoapOptions();

        $this->server = new SoapServer($wsdl, $options);
        $this->configureServer($this->server);
        $this->configureSoapClient($wsdl, $options);
    }

    protected function configureSoapClient($wsdl, $options)
    {
        $this->client = new PhproSoapClient($wsdl, $options);
        $this->client->setHandler(new LocalSoapServerHandle($this->server));
    }

    protected function generateInMemoryWsdl(string $wsdl): string
    {
        return (new InMemoryWsdlProvider())->provide($wsdl);
    }

    protected function provideBasicNonWsdlOptions(): array {
        return [
            'soap_version' => SOAP_1_2,
            'uri' => 'http://localhost/dummysoap',
            'location' => 'http://localhost/dummysoap',
            'cache_wsdl' => WSDL_CACHE_NONE
        ];
    }

    protected function provideBasicWsdlOptions(array $additionalOptions = []): array {
        return array_merge(
            [
                'soap_version' => SOAP_1_2,
                'cache_wsdl' => WSDL_CACHE_NONE
            ],
            $additionalOptions
        );
    }
}
