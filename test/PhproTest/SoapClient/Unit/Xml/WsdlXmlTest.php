<?php

namespace PhproTest\SoapClient\Unit\Xml;

use Phpro\SoapClient\Xml\WsdlXml;
use Phpro\SoapClient\Xml\Xml;
use PHPUnit\Framework\TestCase;

/**
 * Class WsdlXmlTest
 *
 * @package PhproTest\SoapClient\Unit\Xml
 */
class WsdlXmlTest extends TestCase
{

    /**
     * @var \DOMDocument
     */
    private $xml;

    /**
     * Load basic soap XML on startup
     */
    protected function setUp(): void
    {
        $this->xml = new \DOMDocument();
        $this->xml->load(FIXTURE_DIR . '/wsdl/weather-ws.wsdl');
    }

    /**
     * @test
     */
    function it_extends_the_base_xml_class()
    {
        $this->assertInstanceOf(Xml::class, new WsdlXml($this->xml));
    }

    /**
     * @test
     */
    function it_knows_the_wsdl_namespace_uri()
    {
        $xml = new WsdlXml($this->xml);
        $this->assertEquals(
            'http://schemas.xmlsoap.org/wsdl/',
            $xml->getWsdlNamespaceUri()
        );
    }
}
