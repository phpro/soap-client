<?php

namespace PhproTest\SoapClient\Unit\Xml;

use GuzzleHttp\Psr7\Stream;
use Phpro\SoapClient\Xml\Xml;
use PHPUnit\Framework\TestCase;

/**
 * Class XmlTest
 *
 * @package PhproTest\SoapClient\Unit\Xml
 */
class XmlTest extends TestCase
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
    function it_knows_the_root_namespace_uri()
    {
        $xml = new Xml($this->xml);
        $this->assertEquals(
            'http://www.w3.org/2003/05/soap-envelope/',
            $xml->getRootNamespace()
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_register_an_xpath_namespace()
    {
        $xml = new Xml($this->xml);
        $xml->registerNamespace('s', $xml->getRootNamespace());
        $this->assertEquals(
            $xml->xpath('/s:Envelope')->item(0),
            $this->xml->documentElement
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_run_xpath_queries()
    {
        $xml = new Xml($this->xml);
        $this->assertEquals(
            $xml->xpath('/soap:Envelope')->item(0),
            $this->xml->documentElement
        );
    }

    /**
     * @test
     */
    function it_is_possible_to_run_xpath_queries_with_node_context()
    {
        $xml = new Xml($this->xml);
        $documentElement = $xml->xpath('/soap:Envelope')->item(0);
        $header = $xml->xpath('./soap:Header', $documentElement)->item(0);

        $this->assertEquals('soap:Header', $header->nodeName);
    }

    /**
     * @test
     */
    function it_can_access_the_xml_document()
    {
        $xml = new Xml($this->xml);
        $this->assertEquals($this->xml, $xml->getXmlDocument());
    }

    /**
     * @test
     */
    function it_can_access_the_root_element()
    {
        $xml = new Xml($this->xml);
        $this->assertEquals($this->xml->documentElement, $xml->getRootElement());
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

        $xml = Xml::fromStream($stream);
        $this->assertEquals($rawXml, $xml->getXmlDocument()->saveXML());
    }

    /**
     * @test
     */
    function it_is_possible_to_create_from_a_string()
    {
        $rawXml = $this->xml->saveXML();
        $xml = Xml::fromString($rawXml);
        $this->assertEquals($rawXml, $xml->getXmlDocument()->saveXML());
    }


    /**
     * @test
     */
    function it_can_convert_to_a_psr7_stream()
    {
        $rawXml = $this->xml->saveXML();
        $xml = new Xml($this->xml);
        $stream = $xml->toStream();

        $this->assertEquals($rawXml, (string)$stream);
    }

    /**
     * @test
     */
    function it_can_convert_to_a_string()
    {
        $rawXml = $this->xml->saveXML();
        $xml = new Xml($this->xml);

        $this->assertEquals($rawXml, $xml->toString());
    }
}
