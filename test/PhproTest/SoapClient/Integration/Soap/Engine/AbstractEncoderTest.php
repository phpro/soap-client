<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Integration\Soap\Engine;

use Phpro\SoapClient\Soap\Engine\EncoderInterface;
use Phpro\SoapClient\Soap\HttpBinding\SoapRequest;
use Phpro\SoapClient\Xml\SoapXml;

abstract class AbstractEncoderTest extends AbstractIntegrationTest
{
    abstract protected function getEncoder(): EncoderInterface;

    /** @test */
    public function it_encodes_base64_binary()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/base64Binary.wsdl');
        $input = 'myinput';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(base64_encode($input), $result->nodeValue);
    }

    /** @test */
    public function it_handles_simple_content()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/simpleContent.wsdl');
        $input = ['_' => 132, 'country' => 'BE'];
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertEquals(132, $result->nodeValue);
        $this->assertEquals('BE', $result->getAttribute('country'));
    }

    /** @test */
    public function it_handles_xml_entities()
    {
        $this->configureForWsdl(FIXTURE_DIR . '/wsdl/functional/stringContent.wsdl');
        $input = '<\'"ïnpüt"\'>';
        $encoded = $this->getEncoder()->encode($method = 'validate', [$input]);

        $xml = SoapXml::fromString($encoded->getRequest());
        $result = $this->runSingleElementXpathOnBody($xml, './application:validate/input');

        $this->assertSoapRequest($encoded, $xml, $method);
        $this->assertContains(htmlspecialchars($input, ENT_NOQUOTES), $encoded->getRequest());
        $this->assertEquals($input, $result->nodeValue);
    }

    /**
     * we make some assumptions in this method:
     * - Location = body namespace
     * - action = body namespace/Method
     * - No one way configured
     */
    protected function assertSoapRequest(SoapRequest $request, SoapXml $xml, string $method)
    {
        $bodyNamespace = $xml->detectBodyContentsNamespace();
        $this->assertEquals($bodyNamespace, $request->getLocation());
        $this->assertEquals(rtrim($bodyNamespace, '/').'/'.$method, $request->getAction());
        $this->assertTrue($request->isSOAP11() || $request->isSOAP12());
        $this->assertEquals(0, $request->getOneWay());
    }
}
