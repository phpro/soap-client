<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapMetadata;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Generator\DummyMethodArgumentsGenerator;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\MethodsParser;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;
use PHPUnit\Framework\TestCase;

class AbusedClientTest extends TestCase
{
    /**
     * @var AbusedClient
     */
    private $client;

    protected function configureForWsdl(string $wsdl, array $options)
    {
        $this->client = new AbusedClient($wsdl, $options);
    }

    /** @test */
    function it_can_encode_with_typemap()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->__soapCall('validate', ['goodbye']);
        $encoded = $this->client->collectRequest();

        $this->assertStringContainsString('hello', $encoded->getRequest());
        $this->assertStringNotContainsString('goodbye', $encoded->getRequest());
    }

    /** @test */
    function it_can_decode_with_typemap()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->registerResponse($this->generateSoapResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:string">goodbye</output>
</application:validate>
EOB
        ));

        $metadata = new ExtSoapMetadata($this->client);
        $payload = (new DummyMethodArgumentsGenerator($metadata))->generateForSoapCall('validate');
        $decoded = $this->client->__soapCall('validate', $payload);

        $this->assertSame('hello', $decoded);
    }

    /** @test */
    function it_can_decode_with_more_complex_types()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->registerResponse($this->generateSoapResponse(<<<EOB
<application:validate>
    <response xsi:type="application:ValidateResponse">
        <output xsi:type="xsd:string">goodbye</output>
    </response>
</application:validate>
EOB
        ));

        $metadata = new ExtSoapMetadata($this->client);
        $payload = (new DummyMethodArgumentsGenerator($metadata))->generateForSoapCall('validate');
        $decoded = $this->client->__soapCall('validate', $payload);

        $this->assertSame('hello', $decoded);
    }

    private function generateSoapResponse(string $body): SoapResponse
    {
        $response = <<<EORESPONSE
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        $body
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EORESPONSE;

        return new SoapResponse($response);
    }

    private function generateHelloTypeMap(string $xsdType): array
    {
        return [
            [
                'type_name' => $xsdType,
                'type_ns'   => 'http://www.w3.org/2001/XMLSchema',
                'from_xml'  => function () {
                    return 'hello';
                },
                'to_xml'    => function () {
                    return '<d>hello</d>';
                },
            ],
        ];
    }
}
