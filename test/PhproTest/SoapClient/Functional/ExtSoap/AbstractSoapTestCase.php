<?php

declare( strict_types=1 );

namespace PhproTest\SoapClient\Functional\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDriver;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Handler\ExtSoapServerHandle;
use PHPUnit\Framework\TestCase;

abstract class AbstractSoapTestCase extends TestCase
{
    protected function configureSoapDriver(string $wsdl, array $options): ExtSoapDriver
    {
        return ExtSoapDriver::createFromOptions(
            ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache()
        );
    }

    protected function configureServer(string $wsdl, array $options, $object): ExtSoapServerHandle
    {
        $options = ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache();

        $server = new \SoapServer($options->getWsdl(), $options->getOptions());
        $server->setObject($object);

        return new ExtSoapServerHandle($server);
    }
}
