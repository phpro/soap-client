<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\ExtSoap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Transport\ExtSoapServerTransport;
use Soap\ExtSoapEngine\Transport\TraceableTransport;

abstract class AbstractSoapTestCase extends TestCase
{
    protected AbusedClient $client;

    protected function configureSoapDriver(string $wsdl, array $options): ExtSoapDriver
    {
        $driver = ExtSoapDriver::createFromOptions(
            ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache()
        );

        $this->client = $driver->getClient();

        return $driver;
    }

    protected function configureServer(string $wsdl, array $options, $object): TraceableTransport
    {
        $options = ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache();

        $server = new \SoapServer($options->getWsdl(), $options->getOptions());
        $server->setObject($object);

        return new TraceableTransport(
            $this->client,
            new ExtSoapServerTransport($server)
        );
    }
}
