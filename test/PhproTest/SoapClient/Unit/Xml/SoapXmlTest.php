<?php

namespace PhproTest\SoapClient\Unit\Xml;

use GuzzleHttp\Psr7\Stream;
use Phpro\SoapClient\Xml\SoapXml;
use Phpro\SoapClient\Xml\Xml;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapXmlTest
 *
 * @package PhproTest\SoapClient\Unit\Xml
 */
class SoapXmlTest extends TestCase
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
        $this->xml->load(FIXTURE_DIR . '/soap/empty-request-with-head-and-body.xml');
    }

    /**
     * @test
     */
    function it_extends_the_base_xml_class()
    {
        $this->assertInstanceOf(Xml::class, new SoapXml($this->xml));
    }

    /**
     * @test
     */
    function it_knows_the_soap_namespace_uri()
    {
        $xml = new SoapXml($this->xml);
        $this->assertEquals(
            'http://www.w3.org/2003/05/soap-envelope/',
            $xml->getSoapNamespaceUri()
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_register_an_xpath_namespace()
    {
        $xml = new SoapXml($this->xml);
        $xml->registerNamespace('s', $xml->getSoapNamespaceUri());
        $this->assertEquals(
            $xml->xpath('/s:Envelope')->item(0),
            $this->xml->documentElement
        );
    }

    /**
     * @test
     */
    function it_is_easy_to_add_a_new_xlmns_to_the_enveloppe()
    {
        $xml = new SoapXml($this->xml);
        $xml->addEnvelopeNamespace('prefix', $namespace = 'http://namespace.local');
        $this->assertEquals($xml->getEnvelope()->getAttributeNS(SoapXml::XMLNS_XMLNS, 'prefix'), $namespace);
    }

    /**
     * @test
     */
    function it_is_possible_to_run_xpath_queries()
    {
        $xml = new SoapXml($this->xml);
        $this->assertEquals(
            $xml->xpath('/soap:Envelope')->item(0),
            $this->xml->documentElement
        );
    }

    /**
     * @test
     */
    function it_can_access_the_xml_document()
    {
        $xml = new SoapXml($this->xml);
        $this->assertEquals($this->xml, $xml->getXmlDocument());
    }

    /**
     * @test
     */
    function it_can_access_the_soap_enveloppe()
    {
        $xml = new SoapXml($this->xml);
        $this->assertEquals($this->xml->documentElement, $xml->getEnvelope());
    }

    /**
     * @test
     */
    function it_can_access_the_soap_headers()
    {
        $xml = new SoapXml($this->xml);
        $this->assertEquals($xml->getHeaders()->length, 1);
    }

    /**
     * @test
     */
    function it_can_create_a_soap_header()
    {
        $xml = new SoapXml($this->xml);
        $header = $xml->createSoapHeader();

        $this->assertEquals($xml->getSoapNamespaceUri(), $header->namespaceURI);
        $this->assertEquals('soap:Header', $header->tagName);
    }

    /**
     * @test
     */
    function it_can_prepend_a_soap_header()
    {
        $xml = new SoapXml($this->xml);
        $header = $xml->createSoapHeader();
        $xml->prependSoapHeader($header);

        $this->assertEquals($header, $xml->getHeaders()->item(0));
    }

    /**
     * @test
     */
    function it_can_access_the_soap_body()
    {
        $xml = new SoapXml($this->xml);
        $body = $xml->getBody();

        $this->assertEquals('soap:Body', $body->tagName);
    }

    /**
     * @test
     */
    function it_is_possible_to_create_from_a_psr7_stream()
    {
        $rawXml = $this->xml->saveXML();
        $stream = new Stream(fopen('php://memory', 'rwb'));
        $stream->write($rawXml);
        $stream->rewind();

        $xml = SoapXml::fromStream($stream);
        $this->assertEquals($rawXml, $xml->getXmlDocument()->saveXML());
    }

    /**
     * @test
     */
    function it_is_possible_to_create_from_a_already_read_psr7_stream()
    {
        $rawXml = $this->xml->saveXML();
        $stream = new Stream(fopen('php://memory', 'rwb'));
        $stream->write($rawXml);

        $xml = SoapXml::fromStream($stream);
        $this->assertEquals($rawXml, $xml->getXmlDocument()->saveXML());
    }

    /**
     * @test
     */
    function it_can_convert_to_a_psr7_stream()
    {
        $rawXml = $this->xml->saveXML();
        $xml = new SoapXml($this->xml);
        $stream = $xml->toStream();

        $this->assertEquals($rawXml, (string)$stream);
    }
}
