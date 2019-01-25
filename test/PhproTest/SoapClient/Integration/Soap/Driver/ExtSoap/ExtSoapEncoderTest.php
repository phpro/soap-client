<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEncoder;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use PhproTest\SoapClient\Integration\Soap\Engine\AbstractEncoderTest;
use PhproTest\SoapClient\Integration\Soap\Type\ValidateRequest;

class ExtSoapEncoderTest extends AbstractEncoderTest
{
    /**
     * @var ExtSoapEncoder
     */
    private $encoder;

    protected function getEncoder(): EncoderInterface
    {
        return $this->encoder;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->encoder = new ExtSoapEncoder(
            $client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl)
                    ->disableWsdlCache()
                    ->withClassMap(new ClassMapCollection([
                        new ClassMap('MappedValidateRequest', ValidateRequest::class),
                  ]))
            )
        );
    }
}
