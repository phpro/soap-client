<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\DecoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapResponse;

abstract class AbstractDecoderTest extends AbstractIntegrationTest
{
    abstract protected function getDecoder(): DecoderInterface;

    /** @test */
    public function it_decodes_base64_binary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/base64Binary.wsdl');
        $output = base64_encode($expectedOutput = 'decodedoutput');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:base64Binary">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);

        $this->assertEquals($expectedOutput, $decoded);
    }

    /** @test */
    public function it_handles_simple_content()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl');
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="s:SimpleContent" country="BE">132</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals(
            (object)([
                '_' => 132,
                'country' => 'BE',
            ]),
            $decoded
        );
    }

    /** @test */
    public function it_handles_xml_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/stringContent.wsdl');
        $output = htmlspecialchars($expectedOutput = '&lt;\'"ïnpüt"\'&gt;', ENT_NOQUOTES);
        $response = $this->createResponse(<<<EOB
<application:validate>
    <output xsi:type="xsd:string">$output</output>
</application:validate>
EOB
        );

        $decoded = $this->getDecoder()->decode('validate', $response);
        $this->assertEquals($expectedOutput, $decoded);
    }

    protected function createResponse(string $applicationBodyXml): SoapResponse
    {
        return new SoapResponse(<<<EOXML
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        $applicationBodyXml
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOXML
        );
    }
}
