<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapDecoder;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapMetadata;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Generator\DummyMethodArgumentsGenerator;
use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use PhproTest\SoapClient\Integration\Soap\Engine\AbstractDecoderTest;
use PhproTest\SoapClient\Integration\Soap\Type\ValidateResponse;

class ExtSoapDecoderTest extends AbstractDecoderTest
{
    /**
     * @var ExtSoapDecoder
     */
    private $decoder;

    protected function getDecoder(): DecoderInterface
    {
        return $this->decoder;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->decoder = new ExtSoapDecoder(
            $client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl, [])
                    ->disableWsdlCache()
                    ->withClassMap(new ClassMapCollection([
                        new ClassMap('MappedValidateResponse', ValidateResponse::class),
                    ]))
            ),
            new DummyMethodArgumentsGenerator(new ExtSoapMetadata($client))
        );
    }
}
